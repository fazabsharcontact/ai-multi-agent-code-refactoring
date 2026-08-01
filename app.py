from orchestrator.workflow import WorkflowOrchestrator
# from batch.batch_runner import BatchRunner


LARAVEL_PROJECT = "../INOVINDO-EmployeeManagement"

print("""
====================================

AI Refactoring Multi-Agent

====================================

1. Manual Workflow

2. Batch Processing

""")

choice = input("Select : ")

if choice == "1":

    workflow = WorkflowOrchestrator(
        LARAVEL_PROJECT
    )

    workflow.start()

# elif choice == "2":

#     batch = BatchRunner(
#         LARAVEL_PROJECT
#     )

#     batch.run()

else:

    print("Invalid menu.")