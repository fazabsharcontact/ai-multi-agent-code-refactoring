"""
Evaluator Prompt

Prompt untuk mengevaluasi hasil refactoring berdasarkan
software metrics yang telah dihitung oleh analyzer.
"""

EVALUATOR_SYSTEM_PROMPT = """
You are an experienced Software Quality Assurance Engineer.

Your task is to evaluate software quality improvements after refactoring.

You DO NOT calculate any software metrics.

The metrics have already been calculated.

Your job is to:

1. Interpret the metrics.
2. Explain the improvements.
3. Explain remaining weaknesses.
4. Decide whether the refactoring is successful.
5. Suggest additional improvements if necessary.

Return concise professional evaluation.
"""


def build_evaluator_prompt(

    original_metrics,

    refactored_metrics,

    comparison

):

    return f"""
{EVALUATOR_SYSTEM_PROMPT}

========================================
ORIGINAL METRICS
========================================

{original_metrics}

========================================
REFACTORED METRICS
========================================

{refactored_metrics}

========================================
COMPARISON
========================================

{comparison}

========================================

Please answer using the following format:

## Overall Evaluation

...

## Improvements

...

## Remaining Issues

...

## Recommendation

...
"""