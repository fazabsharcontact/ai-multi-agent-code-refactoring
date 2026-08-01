"""
Workflow Orchestrator

Plan-Based Orchestration

Flow:

Controller
    ↓
Analysis Agent
    ↓
Recommendation
    ↓
Continue?
    ↓
Refactoring Agent
    ↓
Continue?
    ↓
Evaluator Agent
    ↓
Evaluation Summary
    ↓
Accept?
"""

from agents.analysis_agent import AnalysisAgent
from agents.refactoring_agent import RefactoringAgent
from agents.evaluator_agent import EvaluatorAgent
from writers.result_writer import ResultWriter
from services.controller_discovery import ControllerDiscovery

class WorkflowOrchestrator:

    def __init__(self, laravel_project_path):

        self.analysis_agent = AnalysisAgent(
            laravel_project_path
        )

        self.refactoring_agent = RefactoringAgent()

        self.evaluator_agent = EvaluatorAgent()

        self.result_writer = ResultWriter()

        self.discovery = ControllerDiscovery(
            laravel_project_path
        )

    # =====================================================

    def start(self):

        print("\n==========================================")
        print(" AI Refactoring Multi-Agent")
        print(" Plan-Based Orchestration")
        print("==========================================")

        controllers = self.discovery.discover()

        print("\nAvailable Controllers\n")

        for i, controller in enumerate(controllers, start=1):

            print(f"[{i}] {controller}")

        print()

        choice = int(

            input("Select Controller : ")

        )

        controller = controllers[choice - 1]

        # =====================================================
        # ANALYSIS
        # =====================================================

        print("\nRunning Analysis Agent...\n")

        analysis_result = self.analysis_agent.analyze(
            controller
        )

        print("==========================================")
        print("ANALYSIS RESULT")
        print("==========================================")

        print(analysis_result["recommendation"])

        print("\n==========================================")

        choice = input(

            "\nContinue to Refactoring?\n\n"

            "[1] Yes\n"

            "[0] Exit\n\n"

            ">> "

        )

        if choice != "1":

            print("\nWorkflow Finished.")

            return None

        # =====================================================
        # REFACTORING
        # =====================================================

        print("\nRunning Refactoring Agent...\n")

        refactoring_result = self.refactoring_agent.refactor(
            analysis_result
        )

        if refactoring_result["status"] == "failed":

            print("\nRefactoring Failed!")

            print(refactoring_result["error"])

            return None

        print("==========================================")
        print("REFACTORING COMPLETED")
        print("==========================================")

        print("\nRefactoring berhasil dilakukan.")

        print("\n==========================================")

        choice = input(

            "\nContinue to Evaluation?\n\n"

            "[1] Yes\n"

            "[0] Exit\n\n"

            ">> "

        )

        if choice != "1":

            print("\nWorkflow Finished.")

            return refactoring_result

        # =====================================================
        # EVALUATION
        # =====================================================

        print("\nRunning Evaluator Agent...\n")

        evaluation_result = self.evaluator_agent.evaluate(
            refactoring_result
        )

        comparison = evaluation_result["comparison"]

        print("\n==========================================")
        print("EVALUATION RESULT")
        print("==========================================")

        print("\nMaintainability Index")

        print(
            f"Before : {comparison['maintainability_index']['before']}"
        )

        print(
            f"After  : {comparison['maintainability_index']['after']}"
        )

        print(
            f"Delta  : {comparison['maintainability_index']['delta']}"
        )

        print("\n------------------------------------------")

        print("\nReusability Index")

        print(
            f"Before : {comparison['reusability_index']['before']}"
        )

        print(
            f"After  : {comparison['reusability_index']['after']}"
        )

        print(
            f"Delta  : {comparison['reusability_index']['delta']}"
        )

        print("\n------------------------------------------")

        print("\nLLM Evaluation\n")

        print(
            evaluation_result["summary"]
        )

        print("\n==========================================")

        choice = input(

            "\nAccept Result?\n\n"

            "[1] Yes\n"

            "[0] Reject\n\n"

            ">> "

        )

        if choice == "1":

            output_path = self.result_writer.save(
                refactoring_result
            )

            print("\n==========================================")
            print("RESULT SAVED")
            print("==========================================")

            print(f"\nSaved to:\n{output_path}")

        else:

            print("\n==========================================")
            print("RESULT REJECTED")
            print("==========================================")

            feedback = input(

                "\nPlease enter your feedback\n"

                "(Example: Don't rename methods, "

                "use Repository Pattern only)\n\n"

                ">> "

            )

            print("\nRunning Refactoring Again...\n")

            refactoring_result = self.refactoring_agent.refactor(

                analysis_result,

                feedback

            )

            print("\n==========================================")
            print("REFACTORING COMPLETED")
            print("==========================================")

            print("\nA new refactored version has been generated.")

            print("\nRun Evaluation again from the workflow.")

        return evaluation_result