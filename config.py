"""
Configuration
"""

import os

from dotenv import load_dotenv
from openai import OpenAI

# =====================================================
# Load Environment
# =====================================================

load_dotenv()

# =====================================================
# OpenAI Client
# =====================================================

client = OpenAI(

    api_key=os.getenv("OPENAI_API_KEY")

)

# =====================================================
# Models
# =====================================================

ANALYSIS_MODEL = "gpt-5.6-luna"

REFACTORING_MODEL = "gpt-5.6-sol"

EVALUATION_MODEL = "gpt-5.6-luna"

# =====================================================
# Workspace
# =====================================================

WORKSPACE_DIR = "workspace"

# =====================================================
# Output Directory
# =====================================================

OUTPUT_DIR = "output"