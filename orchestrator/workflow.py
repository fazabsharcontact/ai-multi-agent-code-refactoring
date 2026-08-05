"""
Workflow Orchestrator

Plan-Based Orchestration

Workflow

GitHub Repository
        │
        ▼
Clone / Pull Repository
        │
        ▼
Validate Laravel Project
        │
        ▼
Discover Controllers
        │
        ▼
Analysis Agent
        │
        ▼
Refactoring Agent
        │
        ▼
Evaluator Agent
        │
        ▼
Save Result
"""

from agents.analysis_agent import AnalysisAgent
from agents.refactoring_agent import RefactoringAgent
from agents.evaluator_agent import EvaluatorAgent

from writers.result_writer import ResultWriter

from services.controller_discovery import ControllerDiscovery
from services.github_repository import GitHubRepository


class WorkflowOrchestrator:

    def __init__(self):

        self.github = GitHubRepository()

        self.result_writer = ResultWriter()

        self.analysis_agent = None

        self.refactoring_agent = RefactoringAgent()

        self.evaluator_agent = EvaluatorAgent()

        self.discovery = None

    # =====================================================

    def start(self):

        print("\n==========================================")
        print(" AI Refactoring Multi-Agent")
        print(" Plan-Based Orchestration")
        print("==========================================")

        github_url = input(
            "\nGitHub Repository URL\n\n>> "
        ).strip()

        print("\nPreparing workspace...\n")

        project_path = self.github.prepare(
            github_url
        )

        self.analysis_agent = AnalysisAgent(
            project_path
        )

        self.discovery = ControllerDiscovery(
            project_path
        )

        print("\n==========================================")
        print("DISCOVER CONTROLLERS")
        print("==========================================")

        controllers = self.discovery.discover()

        if len(controllers) == 0:

            print("\nNo controller found.")

            return

        print(f"\nFound {len(controllers)} controller(s)\n")

        for i, controller in enumerate(
            controllers,
            start=1
        ):

            print(f"[{i}] {controller}")

        print()

        choice = int(

            input("Select Controller : ")

        )

        controller = controllers[
            choice - 1
        ]

        # =====================================================
        # ANALYSIS
        # =====================================================

        print("\n==========================================")
        print("ANALYSIS AGENT")
        print("==========================================")

        print(f"\nSelected Controller : {controller}")

        print("\nRunning Analysis Agent...\n")

        analysis_result = self.analysis_agent.analyze(
            controller
        )

        print("\n==========================================")
        print("REFACTORING RECOMMENDATION")
        print("==========================================\n")

        print(
            analysis_result["recommendation"]
        )

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

        print("\n==========================================")
        print("REFACTORING AGENT")
        print("==========================================")

        print("\nRunning Refactoring Agent...\n")

        refactoring_result = self.refactoring_agent.refactor(
            analysis_result
        )

        if refactoring_result["status"] == "failed":

            print("\n==========================================")
            print("REFACTORING FAILED")
            print("==========================================")

            print(
                refactoring_result["error"]
            )

            return None

        print("\n==========================================")
        print("REFACTORING FINISHED")
        print("==========================================")

        print("\nGenerated Files\n")

        for file in refactoring_result["generated_files"]:

            print(f"✓ {file['path']}")

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

        print("\n==========================================")
        print("EVALUATOR AGENT")
        print("==========================================")

        print("\nRunning Evaluator Agent...\n")

        evaluation_result = self.evaluator_agent.evaluate(
            refactoring_result
        )

        if evaluation_result["status"] == "failed":

            print("\n==========================================")
            print("EVALUATION FAILED")
            print("==========================================")

            print(
                evaluation_result["error"]
            )

            return None

        comparison = evaluation_result["comparison"]

        print("\n==========================================")
        print("MAINTAINABILITY INDEX")
        print("==========================================")

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

        print("REUSABILITY INDEX")

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

        print("CLEAN CODE")

        clean_before = evaluation_result["original"]["clean_code"]

        clean_after = evaluation_result["refactored"]["clean_code"]

        print(
            f"Before : {clean_before}"
        )

        print(
            f"After  : {clean_after}"
        )

        print("\n------------------------------------------")

        print("CODE SMELL")

        smell_before = evaluation_result["original"]["code_smell"]

        smell_after = evaluation_result["refactored"]["code_smell"]

        print(
            f"Before : {smell_before}"
        )

        print(
            f"After  : {smell_after}"
        )

        print("\n------------------------------------------")

        print("LLM EVALUATION SUMMARY\n")

        print(
            evaluation_result["summary"]
        )

        print("\n==========================================")

        # =====================================================
        # ACCEPT RESULT
        # =====================================================

        choice = input(

            "\nAccept Refactoring Result?\n\n"

            "[1] Yes\n"

            "[0] Reject\n\n"

            ">> "

        )

        if choice == "1":

            output = self.result_writer.save(
                refactoring_result
            )

            print("\n==========================================")
            print("RESULT SAVED")
            print("==========================================")

            print(f"\nOutput Folder :\n{output['folder']}")

            print("\nGenerated Files\n")

            for file in output["files"]:

                print(f"✓ {file}")

            print("\n==========================================")
            print("WORKFLOW COMPLETED")
            print("==========================================")

        else:

            print("\n==========================================")
            print("REFACTORING REJECTED")
            print("==========================================")

            feedback = input(

                "\nEnter your feedback\n"

                "(Example:\n"

                "- Use Repository Pattern only\n"

                "- Don't rename methods\n"

                "- Use Service Layer\n\n"

                ">> "

            )

            print("\n==========================================")
            print("RE-RUN REFACTORING")
            print("==========================================")

            refactoring_result = self.refactoring_agent.refactor(

                analysis_result,

                feedback

            )

            if refactoring_result["status"] == "failed":

                print("\nRefactoring Failed.")

                print(
                    refactoring_result["error"]
                )

                return None

            print("\nNew refactoring generated.")

            print("\nGenerated Files\n")

            for file in refactoring_result["generated_files"]:

                print(f"✓ {file['path']}")

            print(

                "\nPlease run Evaluation again "

                "to validate the new version."

            )

        return evaluation_result