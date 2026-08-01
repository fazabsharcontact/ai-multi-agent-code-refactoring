from typing import TypedDict, List, Dict, Any


class AgentState(TypedDict):
    """
    Shared State seluruh workflow Multi-Agent
    """

    # ===============================
    # INPUT
    # ===============================

    controller_path: str
    source_code: str

    # ===============================
    # Maintainability Analysis
    # ===============================

    maintainability_metrics: Dict[str, Any]

    # LOC
    # CC
    # Halstead
    # MI

    # ===============================
    # Reusability Analysis
    # ===============================

    reusability_metrics: Dict[str, Any]

    # Coupling
    # REI

    # ===============================
    # Clean Code Analysis
    # ===============================

    clean_code_violations: List[str]

    # ===============================
    # Code Smell Analysis
    # ===============================

    code_smells: List[str]

    # ===============================
    # Recommendation
    # ===============================

    refactoring_recommendations: List[str]

    target_patterns: List[str]

    # contoh:
    # Service Layer
    # Repository Pattern

    # ===============================
    # Refactoring
    # ===============================

    refactored_code: str

    # ===============================
    # Evaluation
    # ===============================

    evaluation: Dict[str, Any]

    # MI Before
    # MI After
    # REI Before
    # REI After

    # ===============================
    # Workflow
    # ===============================

    current_step: str
    status: str

    # ===============================
    # Human in the Loop
    # ===============================

    approved: bool
    reviewer_notes: str

    # ===============================
    # History
    # ===============================

    history: List[str]