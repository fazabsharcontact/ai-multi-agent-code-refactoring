"""
Refactoring Agent

Agent yang bertugas melakukan refactoring source code
berdasarkan hasil analisis menggunakan Large Language Model.
"""

from config import client, REFACTORING_MODEL
from prompts.refactoring_prompt import build_refactoring_prompt


class RefactoringAgent:

    def __init__(self):
        self.client = client

    # =====================================================
    # Utility
    # =====================================================

    def clean_response(self, text: str) -> str:
        """
        Menghapus markdown ```php dari output LLM.
        """

        text = text.strip()

        if text.startswith("```php"):
            text = text.replace("```php", "", 1)

        elif text.startswith("```"):
            text = text.replace("```", "", 1)

        if text.endswith("```"):
            text = text[:-3]

        return text.strip()

    # =====================================================
    # Refactor
    # =====================================================

    def refactor(
        self,
        analysis_result,
        user_feedback=""
    ):

        source_code = analysis_result["source_code"]

        prompt = build_refactoring_prompt(
            source_code,
            analysis_result,
            user_feedback
        )

        try:

            response = self.client.responses.create(

                model=REFACTORING_MODEL,

                input=prompt

            )

            refactored_code = self.clean_response(
                response.output_text
            )

            return {

                "status": "success",

                "controller": analysis_result["controller"],

                "original_code": source_code,

                "refactored_code": refactored_code,

                "analysis": analysis_result,

                "prompt": prompt

            }

        except Exception as e:

            return {

                "status": "failed",

                "controller": analysis_result["controller"],

                "original_code": source_code,

                "refactored_code": None,

                "analysis": analysis_result,

                "prompt": prompt,

                "error": str(e)

            }