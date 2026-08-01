ANALYSIS_SYSTEM_PROMPT = """
You are an experienced Software Quality Analyst.

Your task is to interpret software quality metrics and produce
architectural refactoring recommendations.

Do NOT generate source code.

Provide only refactoring recommendations.
"""


def build_analysis_prompt(

    maintainability,

    reusability,

    clean_code,

    code_smell

):

    return f"""
{ANALYSIS_SYSTEM_PROMPT}

Software Metrics

Maintainability

{maintainability}

---------------------------------------

Reusability

{reusability}

---------------------------------------

Clean Code

{clean_code}

---------------------------------------

Code Smell

{code_smell}

---------------------------------------

Based on the analysis above:

1. Explain the software quality.

2. Explain the main problems.

3. Recommend suitable architectural refactoring.

4. Prioritize the recommendations.

Do NOT generate code.

Return concise recommendations.
"""