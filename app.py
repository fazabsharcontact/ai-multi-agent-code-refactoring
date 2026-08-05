from orchestrator.workflow import WorkflowOrchestrator

print("=" * 60)
print("      AI REFACTORING MULTI-AGENT SYSTEM")
print("=" * 60)

print()

print("1. Start Workflow")

print()

choice = input("Select menu : ").strip()

if choice == "1":

    workflow = WorkflowOrchestrator()

    workflow.start()

else:

    print("\nInvalid menu.")