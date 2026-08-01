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


class AnalysisAgent:

    def __init__(self, laravel_project_path):

        self.reader = LaravelReader(laravel_project_path)

        self.maintainability = MaintainabilityAnalyzer()

        self.reusability = ReusabilityAnalyzer()

        self.clean_code = CleanCodeAnalyzer()

        self.code_smell = CodeSmellAnalyzer()

    # =====================================================

    def analyze(self, controller_path):

        # =================================================
        # Read Source Code
        # =================================================

        source_code = self.reader.read_controller(
            controller_path
        )

        # =================================================
        # Maintainability
        # =================================================

        maintainability_result = self.maintainability.analyze(
            source_code
        )

        # =================================================
        # Coupling
        # =================================================

        coupling_result = self.coupling.analyze(
            source_code
        )

        # =================================================
        # Reusability
        # =================================================

        reusability_result = self.reusability.analyze(

            loc=maintainability_result["loc"],

            cc=maintainability_result[
                "cyclomatic_complexity"
            ],

            ce=coupling_result["ce"]

        )

        # =================================================
        # Clean Code
        # =================================================

        clean_code_result = self.clean_code.analyze(
            source_code
        )

        # =================================================
        # Code Smell
        # =================================================

        code_smell_result = self.code_smell.analyze(
            source_code
        )

        # =================================================
        # Build Prompt
        # =================================================

        prompt = build_analysis_prompt(

            maintainability_result,

            reusability_result,

            clean_code_result,

            code_smell_result

        )

        # =================================================
        # LLM Recommendation
        # =================================================

        response = client.responses.create(

            model=ANALYSIS_MODEL,

            input=prompt

        )

        recommendation = response.output_text

        # =================================================
        # Return
        # =================================================

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