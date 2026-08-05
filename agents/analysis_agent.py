"""
Analysis Agent

Agent pertama pada workflow Multi-Agent.

Tugas:
1. Membaca source code controller
2. Menjalankan seluruh analyzer
3. Mengirim hasil analisis ke LLM
4. Menghasilkan rekomendasi refactoring
"""

from services.laravel_reader import LaravelReader
from services.maintainability_analyzer import MaintainabilityAnalyzer
from services.reusability_analyzer import ReusabilityAnalyzer
from services.clean_code_analyzer import CleanCodeAnalyzer
from services.code_smell_analyzer import CodeSmellAnalyzer
from services.coupling_analyzer import CouplingAnalyzer

from prompts.analysis_prompt import build_analysis_prompt
from config import client, ANALYSIS_MODEL

import time


class AnalysisAgent:

    def __init__(self, laravel_project_path):

        self.reader = LaravelReader(
            laravel_project_path
        )

        self.maintainability = MaintainabilityAnalyzer()

        self.coupling = CouplingAnalyzer()

        self.reusability = ReusabilityAnalyzer()

        self.clean_code = CleanCodeAnalyzer()

        self.code_smell = CodeSmellAnalyzer()

    # =====================================================

    # =====================================================
    # Progress
    # =====================================================

    def step(self, title):

        print(f"\n{title}")

        time.sleep(0.3)

    def analyze(self, controller_path):

        print("\n==========================================")
        print("ANALYSIS AGENT")
        print("==========================================")

        print(f"\nController")
        print(f"  {controller_path}")

        self.step("Reading source code...")

        source_code = self.reader.read_controller(
            controller_path
        )

        print("✓ Source code loaded.")

        # =================================================
        # Maintainability
        # =================================================

        self.step("Calculating Maintainability Index...")

        maintainability_result = self.maintainability.analyze(
            source_code
        )

        print(
            f"✓ MI : {maintainability_result['maintainability_index']}"
        )

        # =================================================
        # Coupling
        # =================================================

        self.step("Analyzing Coupling...")

        coupling_result = self.coupling.analyze(
            source_code
        )

        print(f"✓ Ce : {coupling_result['ce']}")

        # =================================================
        # Reusability
        # =================================================

        self.step("Calculating Reusability Index...")

        reusability_result = self.reusability.analyze(

            loc=maintainability_result["loc"],

            cc=maintainability_result[
                "cyclomatic_complexity"
            ],

            ce=coupling_result["ce"]

        )

        print(
            f"✓ RI : {reusability_result['reusability_index']}"
        )

        # =================================================
        # Clean Code
        # =================================================

        self.step("Checking Clean Code violations...")

        clean_code_result = self.clean_code.analyze(
            source_code
        )

        print("✓ Clean Code analysis completed.")

        # =================================================
        # Code Smell
        # =================================================

        self.step("Detecting Code Smells...")

        code_smell_result = self.code_smell.analyze(
            source_code
        )

        print("✓ Code Smell analysis completed.")

        # =================================================
        # Prompt
        # =================================================

        self.step("Building prompt for GPT Luna...")

        prompt = build_analysis_prompt(

            maintainability_result,

            reusability_result,

            clean_code_result,

            code_smell_result

        )

        print("✓ Prompt generated.")

        # =================================================
        # LLM
        # =================================================

        self.step("Sending prompt to GPT Luna...")

        response = client.responses.create(

            model=ANALYSIS_MODEL,

            input=prompt

        )

        recommendation = response.output_text

        print("✓ Recommendation received.")

        # =================================================
        # Return
        # =================================================

        print("\nAnalysis Agent completed successfully.")

        return {

            "controller": controller_path,

            "source_code": source_code,

            "maintainability": maintainability_result,

            "coupling": coupling_result,

            "reusability": reusability_result,

            "clean_code": clean_code_result,

            "code_smell": code_smell_result,

            "recommendation": recommendation

        }