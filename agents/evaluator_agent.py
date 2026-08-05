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
        # Progress
        # =====================================================

    def step(self, message):

        print(f"\n{message}")

        import time

        time.sleep(0.4)

    # =====================================================

    def evaluate(self, refactoring_result):

        print("\n==========================================")
        print("EVALUATOR AGENT")
        print("==========================================")

        print(f"\nController : {refactoring_result['controller']}")

        original_code = refactoring_result["original_code"]

        # Ambil controller hasil refactoring
        refactored_code = None

        for file in refactoring_result["generated_files"]:

            if "Controller" in file["path"]:

                refactored_code = file["content"]

                break


        if refactored_code is None:

            return {

                "status": "failed",

                "error": "Refactored controller not found"

            }

        # =====================================================
        # ORIGINAL
        # =====================================================

        self.step("Calculating original software metrics...")

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

        print("✓ Original metrics calculated.")

        # =====================================================
        # REFACTORED
        # =====================================================

        self.step("Calculating refactored software metrics...")

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

        print("✓ Refactored metrics calculated.")

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

        self.step("Building evaluation prompt...")

        print("✓ Prompt generated.")

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

        self.step("Sending metrics to GPT Luna...")

        print("✓ Evaluation received.")

        response = self.client.responses.create(

            model=EVALUATION_MODEL,

            input=prompt

        )

        # =====================================================
        # RETURN
        # =====================================================

        print("\n==========================================")
        print("EVALUATION COMPLETED")
        print("==========================================")

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