import json
from pathlib import Path

import numpy as np


BASE_DIR = Path(__file__).resolve().parent

AGENT_RESULT = (
    BASE_DIR /
    "data" /
    "agent_results" /
    "evaluator_results.json"
)

LLM_RESULT = (
    BASE_DIR /
    "results" /
    "GajiController_llm_judge.json"   
)

OUTPUT_DIR = BASE_DIR / "results"
OUTPUT_DIR.mkdir(exist_ok=True)


CRITERIA = [
    "responsibility_separation",
    "modularity",
    "readability",
    "solid",
    "architecture"
]


def load_json(path):
    with open(path, "r", encoding="utf-8") as file:
        return json.load(file)


def compare_scores(agent_scores, llm_scores):

    differences = {}

    agent_values = []
    llm_values = []

    for criterion in CRITERIA:

        agent_score = agent_scores.get(
            criterion,
            None
        )

        llm_score = llm_scores.get(
            criterion,
            None
        )

        if agent_score is None or llm_score is None:
            continue

        difference = abs(
            agent_score - llm_score
        )

        differences[criterion] = {
            "agent_score": agent_score,
            "llm_judge_score": llm_score,
            "absolute_difference": difference
        }

        agent_values.append(agent_score)
        llm_values.append(llm_score)

    mean_difference = (
        np.mean(
            [
                item["absolute_difference"]
                for item in differences.values()
            ]
        )
        if differences
        else None
    )

    return differences, mean_difference


def main():

    agent_data = load_json(AGENT_RESULT)
    llm_data = load_json(LLM_RESULT)

    agent_scores = agent_data["scores"]
    llm_scores = llm_data["scores"]

    differences, mean_difference = compare_scores(
        agent_scores,
        llm_scores
    )

    result = {
        "controller": "Admin_GajiController.php",
        "comparison": differences,
        "mean_absolute_difference": mean_difference
    }

    output_path = (
        OUTPUT_DIR /
        "Admin_GajiController_agent_judge_comparison.json"
    )

    with open(
        output_path,
        "w",
        encoding="utf-8"
    ) as file:

        json.dump(
            result,
            file,
            indent=4,
            ensure_ascii=False
        )

    print(json.dumps(
        result,
        indent=4,
        ensure_ascii=False
    ))


if __name__ == "__main__":
    main()