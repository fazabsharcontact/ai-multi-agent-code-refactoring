import json
import re
import glob
from pathlib import Path
import numpy as np

BASE_DIR = Path(__file__).resolve().parent
RESULT_DIR = BASE_DIR / "results"

CRITERIA_KEYWORDS = {
    "prompting": ["prompting"],
    "responsibility_separation": ["pemisahan tanggung jawab"],
    "modularity": ["modularitas"],
    "readability": ["keterbacaan"],
    "solid": ["solid"],
    "architecture": ["arsitektur"],
}


def load_json(path):
    with open(path, "r", encoding="utf-8") as file:
        return json.load(file)


def extract_scores_from_text(text):
    """
    Fallback parser: mencari skor per kriteria langsung dari teks
    naratif markdown, untuk hasil run lama yang belum memiliki
    field 'scores' terstruktur. Mencari kemunculan pertama nama
    kriteria, lalu pola 'X/5' di sekitarnya.
    """
    scores = {}
    lowered = text.lower()

    for key, keywords in CRITERIA_KEYWORDS.items():
        for keyword in keywords:
            idx = lowered.find(keyword)
            if idx == -1:
                continue

            window = text[idx: idx + 200]
            match = re.search(r"(\d)\s*/\s*5", window)
            if match:
                scores[key] = int(match.group(1))
                break

    return scores if scores else None


def get_scores(result_data):
    scores = result_data.get("scores")
    if scores:
        return {k: v for k, v in scores.items() if k in CRITERIA_KEYWORDS}

    return extract_scores_from_text(result_data.get("evaluation", ""))


def analyze_uncertainty(controller_stem):
    pattern = str(RESULT_DIR / f"{controller_stem}_llm_judge_run*.json")
    run_files = sorted(glob.glob(pattern))

    if not run_files:
        print(f"Tidak ditemukan file run untuk '{controller_stem}'.")
        print(f"Pola yang dicari: {pattern}")
        return None

    all_scores = {key: [] for key in CRITERIA_KEYWORDS}
    overall_per_run = []

    for file_path in run_files:
        data = load_json(file_path)
        scores = get_scores(data)

        if not scores:
            print(f"Peringatan: skor tidak ditemukan di {file_path}, dilewati.")
            continue

        run_values = []
        for key in CRITERIA_KEYWORDS:
            if key in scores:
                all_scores[key].append(scores[key])
                run_values.append(scores[key])

        if run_values:
            overall_per_run.append(np.mean(run_values))

    summary = {}
    for key, values in all_scores.items():
        if not values:
            continue
        summary[key] = {
            "n_runs": len(values),
            "values": values,
            "mean": round(float(np.mean(values)), 3),
            "std": round(float(np.std(values, ddof=1)) if len(values) > 1 else 0.0, 3),
            "min": min(values),
            "max": max(values),
            "range": max(values) - min(values),
        }

    overall_summary = {
        "n_runs": len(overall_per_run),
        "values": [round(v, 3) for v in overall_per_run],
        "mean": round(float(np.mean(overall_per_run)), 3) if overall_per_run else None,
        "std": round(float(np.std(overall_per_run, ddof=1)), 3) if len(overall_per_run) > 1 else 0.0,
        "min": round(min(overall_per_run), 3) if overall_per_run else None,
        "max": round(max(overall_per_run), 3) if overall_per_run else None,
    }

    result = {
        "controller": controller_stem,
        "files_analyzed": run_files,
        "per_criterion": summary,
        "overall": overall_summary,
    }

    output_path = RESULT_DIR / f"{controller_stem}_uncertainty.json"
    with open(output_path, "w", encoding="utf-8") as file:
        json.dump(result, file, indent=4, ensure_ascii=False)

    print(json.dumps(result, indent=4, ensure_ascii=False))
    print(f"\nHasil disimpan: {output_path}")

    return result


if __name__ == "__main__":
    analyze_uncertainty("GajiController")