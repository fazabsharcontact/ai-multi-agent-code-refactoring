"""
Evaluator Agent

Agent yang bertugas mengevaluasi hasil refactoring.

Software metrics dihitung menggunakan analyzer.

LLM hanya digunakan untuk memberikan interpretasi hasil evaluasi.
"""

from services.maintainability_analyzer import MaintainabilityAnalyzer
from services.reusability_analyzer import ReusabilityAnalyzer
from services.coupling_analyzer import CouplingAnalyzer
from services.clean_code_analyzer import CleanCodeAnalyzer
from services.code_smell_analyzer import CodeSmellAnalyzer

from prompts.evaluator_prompt import build_evaluator_prompt

from config import client, EVALUATION_MODEL


class EvaluatorAgent:

    def __init__(self):

        self.client = client

        self.maintainability = MaintainabilityAnalyzer()

        self.reusability = ReusabilityAnalyzer()

        self.coupling = CouplingAnalyzer()

        self.clean_code = CleanCodeAnalyzer()

        self.code_smell = CodeSmellAnalyzer()

    # =====================================================

    def evaluate(self, refactoring_result):

        original_code = refactoring_result["original_code"]

        refactored_code = refactoring_result["refactored_code"]

        # =====================================================
        # ORIGINAL
        # =====================================================

        original_mi = self.maintainability.analyze(
            original_code
        )

        original_ce = self.coupling.analyze(
            original_code
        )

        original_rei = self.reusability.analyze(

            loc=original_mi["loc"],

            cc=original_mi["cyclomatic_complexity"],

            ce=original_ce["ce"]

        )

        original_clean = self.clean_code.analyze(
            original_code
        )

        original_smell = self.code_smell.analyze(
            original_code
        )

        # =====================================================
        # REFACTORED
        # =====================================================

        refactored_mi = self.maintainability.analyze(
            refactored_code
        )

        refactored_ce = self.coupling.analyze(
            refactored_code
        )

        refactored_rei = self.reusability.analyze(

            loc=refactored_mi["loc"],

            cc=refactored_mi["cyclomatic_complexity"],

            ce=refactored_ce["ce"]

        )

        refactored_clean = self.clean_code.analyze(
            refactored_code
        )

        refactored_smell = self.code_smell.analyze(
            refactored_code
        )

        # =====================================================
        # COMPARISON
        # =====================================================

        comparison = {

            "maintainability_index": {

                "before": original_mi["maintainability_index"],

                "after": refactored_mi["maintainability_index"],

                "delta": round(

                    refactored_mi["maintainability_index"]

                    -

                    original_mi["maintainability_index"]

                ,2)

            },

            "reusability_index": {

                "before": original_rei["reusability_index"],

                "after": refactored_rei["reusability_index"],

                "delta": round(

                    refactored_rei["reusability_index"]

                    -

                    original_rei["reusability_index"]

                ,2)

            }

        }

        # =====================================================
        # BUILD PROMPT
        # =====================================================

        prompt = build_evaluator_prompt(

            {

                "maintainability": original_mi,

                "coupling": original_ce,

                "reusability": original_rei,

                "clean_code": original_clean,

                "code_smell": original_smell

            },

            {

                "maintainability": refactored_mi,

                "coupling": refactored_ce,

                "reusability": refactored_rei,

                "clean_code": refactored_clean,

                "code_smell": refactored_smell

            },

            comparison

        )

        # =====================================================
        # LLM SUMMARY
        # =====================================================

        response = self.client.responses.create(

            model=EVALUATION_MODEL,

            input=prompt

        )

        # =====================================================
        # RETURN
        # =====================================================

        return {

            "status": "success",

            "controller": refactoring_result["controller"],

            "original": {

                "maintainability": original_mi,

                "coupling": original_ce,

                "reusability": original_rei,

                "clean_code": original_clean,

                "code_smell": original_smell

            },

            "refactored": {

                "maintainability": refactored_mi,

                "coupling": refactored_ce,

                "reusability": refactored_rei,

                "clean_code": refactored_clean,

                "code_smell": refactored_smell

            },

            "comparison": comparison,

            "summary": response.output_text

        }