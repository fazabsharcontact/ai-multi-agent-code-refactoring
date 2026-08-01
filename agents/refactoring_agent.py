"""
Refactoring Agent

Agent yang bertugas melakukan refactoring source code
berdasarkan hasil analisis menggunakan Large Language Model.
"""

import re

from config import client, REFACTORING_MODEL
from prompts.refactoring_prompt import build_refactoring_prompt


class RefactoringAgent:

    def __init__(self):

        self.client = client

    # =====================================================
    # Clean Response
    # =====================================================

    def clean_response(self, text: str) -> str:

        text = text.strip()

        if text.startswith("```php"):
            text = text.replace("```php", "", 1)

        elif text.startswith("```"):
            text = text.replace("```", "", 1)

        if text.endswith("```"):
            text = text[:-3]

        return text.strip()

    # =====================================================
    # Parse Generated Files
    # =====================================================

    def parse_files(self, response_text):

        files = []

        pattern = r"===== FILE =====\s*(.*?)\n(.*?)(?=(?:===== FILE =====)|$)"

        matches = re.findall(
            pattern,
            response_text,
            flags=re.DOTALL
        )

        for path, content in matches:

            files.append({

                "path": path.strip(),

                "content": content.strip()

            })

        return files

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

            raw_output = self.clean_response(
                response.output_text
            )

            generated_files = self.parse_files(
                raw_output
            )

            return {

                "status": "success",

                "controller": analysis_result["controller"],

                "original_code": source_code,

                "generated_files": generated_files,

                "raw_output": raw_output,

                "analysis": analysis_result,

                "prompt": prompt

            }

        except Exception as e:

            return {

                "status": "failed",

                "controller": analysis_result["controller"],

                "original_code": source_code,

                "generated_files": [],

                "raw_output": None,

                "analysis": analysis_result,

                "prompt": prompt,

                "error": str(e)

            }