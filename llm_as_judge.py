import os
import json
import re
from pathlib import Path

from llm_client import ask_llm, MODEL
from prompt import (
    JUDGE_SYSTEM_PROMPT,
    build_judge_prompt,
    ORIGINAL_REFACTORING_PROMPT
)


BASE_DIR = Path(__file__).resolve().parent

BEFORE_DIR = BASE_DIR / "data" / "before"
AFTER_DIR = BASE_DIR / "data" / "after"
RESULT_DIR = BASE_DIR / "results"

RESULT_DIR.mkdir(exist_ok=True)


def read_code(path):
    if not path.exists():
        raise FileNotFoundError(f"File tidak ditemukan: {path}")

    return path.read_text(
        encoding="utf-8",
        errors="ignore"
    )


def read_related_files(directory, filenames):
    combined = ""
    for filename in filenames:
        path = directory / filename
        if path.exists():
            code = read_code(path)
            combined += f"\n// === File: {filename} ===\n{code}\n"
        else:
            combined += f"\n// === File: {filename} (tidak ditemukan) ===\n"
    return combined


def extract_scores(result_text):
    """
    Mencari blok '===== SCORES_JSON =====' di akhir teks hasil LLM
    dan mem-parsing JSON di dalamnya. Mengembalikan None kalau
    blok tidak ditemukan atau JSON tidak valid, supaya narasi
    (evaluation) tetap tersimpan meskipun parsing skor gagal.
    """
    marker = "===== SCORES_JSON ====="
    if marker not in result_text:
        return None

    json_part = result_text.split(marker, 1)[1].strip()

    # Ambil blok JSON pertama saja (dari '{' sampai '}' penutup yang cocok)
    match = re.search(r"\{.*\}", json_part, re.DOTALL)
    if not match:
        return None

    try:
        return json.loads(match.group(0))
    except json.JSONDecodeError:
        return None


def evaluate_controller(controller_name, related_files=None, include_original_prompt=True):
    before_path = BEFORE_DIR / controller_name
    after_path = AFTER_DIR / controller_name

    before_code = read_code(before_path)
    after_code = read_code(after_path)

    if related_files:
        after_code += read_related_files(AFTER_DIR, related_files)

    original_prompt = ORIGINAL_REFACTORING_PROMPT if include_original_prompt else None

    prompt = build_judge_prompt(
        before_code,
        after_code,
        original_prompt
    )

    print(f"\nEvaluating: {controller_name}")

    result = ask_llm(
        JUDGE_SYSTEM_PROMPT,
        prompt
    )

    scores = extract_scores(result)

    if scores is None:
        print("Peringatan: blok SCORES_JSON tidak ditemukan atau tidak valid.")

    output = {
        "controller": controller_name,
        "model": MODEL,
        "scores": scores,
        "evaluation": result
    }

    output_path = (
        RESULT_DIR /
        f"{Path(controller_name).stem}_llm_judge.json"
    )

    with open(output_path, "w", encoding="utf-8") as file:
        json.dump(
            output,
            file,
            indent=4,
            ensure_ascii=False
        )

    print(f"Hasil disimpan: {output_path}")

    return output


if __name__ == "__main__":

    evaluate_controller(
        "GajiController.php",
        related_files=[
            "GajiService.php",
            "SaveGajiRequest.php",
            "CekGajiPegawaiRequest.php",
            "GajiRepositoryInterface.php",
            "EloquentGajiRepository.php",
            "PegawaiRepositoryInterface.php",
            "EloquentPegawaiRepository.php",
            "GajiReferenceRepositoryInterface.php",
            "EloquentGajiReferenceRepository.php",
            "RepositoryServiceProvider.php"
        ]
    )