"""
Refactoring Agent

Agent yang bertugas melakukan refactoring source code
berdasarkan hasil analisis menggunakan Large Language Model.
"""

import re

from config import client, REFACTORING_MODEL
from prompts.refactoring_prompt import build_refactoring_prompt

import re
import time


class RefactoringAgent:

    def __init__(self):

        self.client = client

    # =====================================================
    # Progress
    # =====================================================

    def step(self, message):

        print(f"\n{message}")

        time.sleep(0.4)

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

        pattern = (
            r"===== STEP =====\s*(.*?)\s*"
            r"===== FILE =====\s*(.*?)\n"
            r"(.*?)(?=(?:===== STEP =====)|$)"
        )

        matches = re.findall(
            pattern,
            response_text,
            flags=re.DOTALL
        )

        for step, path, content in matches:

            files.append({

                "step": step.strip(),

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

        print("\n==========================================")
        print("REFACTORING AGENT")
        print("==========================================")

        print(f"\nController : {analysis_result['controller']}")

        try:

            # ============================================
            # Read Source
            # ============================================

            self.step("Reading analysis result...")

            source_code = analysis_result["source_code"]

            print("✓ Analysis loaded.")

            # ============================================
            # Build Prompt
            # ============================================

            self.step("Building prompt...")

            prompt = build_refactoring_prompt(
                source_code,
                analysis_result,
                user_feedback
            )

            print("✓ Prompt generated.")

            # ============================================
            # GPT
            # ============================================

            self.step("Sending prompt to GPT Sol...")

            response = self.client.responses.create(
                model=REFACTORING_MODEL,
                input=prompt
            )

            print("✓ GPT response received.")

            # ============================================
            # Clean
            # ============================================

            self.step("Cleaning response...")

            raw_output = self.clean_response(
                response.output_text
            )

            print("✓ Response cleaned.")

            # ============================================
            # Parse Files
            # ============================================

            self.step("Generating Laravel structure...")

            generated_files = self.parse_files(
                raw_output
            )

            print(
                f"✓ {len(generated_files)} file(s) generated."
            )

            print("\n==========================================")
            print("REFACTORING PROCESS")
            print("==========================================")

            for file in generated_files:

                print(f"\n{file['step']}...")

                time.sleep(0.25)

                print(f"✓ {file['path']}")

                time.sleep(0.15)

            print("\n==========================================")
            print("REFACTORING FINISHED")
            print("==========================================")

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

            print("\n==========================================")
            print("REFACTORING FAILED")
            print("==========================================")

            print(e)

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