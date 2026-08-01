"""
Configuration
"""

import os

from dotenv import load_dotenv
from openai import OpenAI

load_dotenv()

client = OpenAI(
    api_key=os.getenv("OPENAI_API_KEY")
)

ANALYSIS_MODEL = "gpt-5.6-luna"

REFACTORING_MODEL = "gpt-5.6-sol"

EVALUATION_MODEL = "gpt-5.6-luna"