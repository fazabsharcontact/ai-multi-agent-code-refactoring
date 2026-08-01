"""
Batch Runner

Menjalankan workflow terhadap seluruh
controller Laravel secara otomatis.
"""

from services.controller_discovery import ControllerDiscovery
from agents.analysis_agent import AnalysisAgent
from agents.refactoring_agent import RefactoringAgent
from agents.evaluator_agent import EvaluatorAgent
from writers.result_writer import ResultWriter


class BatchRunner:

    def __init__(self, laravel_project_path):

        self.discovery = ControllerDiscovery(
            laravel_project_path
        )

        self.analysis = AnalysisAgent(
            laravel_project_path
        )

        self.refactoring = RefactoringAgent()

        self.evaluator = EvaluatorAgent()

        self.writer = ResultWriter()

    # =====================================================

    def run(self):

        controllers = self.discovery.discover()

        print("\n===================================")
        print("BATCH PROCESS")
        print("===================================")

        print(f"\nTotal Controller : {len(controllers)}\n")

        for i, controller in enumerate(controllers, start=1):

            print(
                f"\n[{i}/{len(controllers)}] {controller}"
            )

            try:

                # ===========================================
                # ANALYSIS
                # ===========================================

                analysis_result = self.analysis.analyze(
                    controller
                )

                # ===========================================
                # REFACTORING
                # ===========================================

                refactoring_result = self.refactoring.refactor(
                    analysis_result
                )

                if refactoring_result["status"] == "failed":

                    print("Refactoring Failed")

                    continue

                # ===========================================
                # EVALUATION
                # ===========================================

                evaluation_result = self.evaluator.evaluate(
                    refactoring_result
                )

                # ===========================================
                # SAVE
                # ===========================================

                output = self.writer.save(
                    refactoring_result
                )

                print(f"Saved : {output}")

            except Exception as e:

                print(f"ERROR : {e}")

        print("\n===================================")
        print("Batch Finished")
        print("===================================")