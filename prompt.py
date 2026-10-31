JUDGE_SYSTEM_PROMPT = """
Anda adalah evaluator kualitas kode perangkat lunak yang bertindak sebagai
LLM-as-a-Judge.

Tugas Anda adalah menilai hasil refactoring kode PHP Laravel secara objektif.

Anda harus membandingkan:
1. kode sebelum refactoring,
2. kode setelah refactoring,
3. tujuan refactoring,
4. kriteria penilaian yang diberikan.

Jangan menilai berdasarkan apakah kode terlihat lebih panjang atau lebih pendek
saja. Fokus pada struktur, pemisahan tanggung jawab, modularitas, keterbacaan,
prinsip SOLID, dan penerapan arsitektur.

Gunakan skala 1 sampai 5:

1 = sangat buruk / tidak terpenuhi
2 = kurang
3 = cukup
4 = baik
5 = sangat baik

Berikan alasan yang spesifik berdasarkan kode yang diberikan.

Jangan mengarang fitur atau struktur yang tidak terdapat pada kode.
"""

ORIGINAL_REFACTORING_PROMPT = """
You are a Senior Laravel Software Architect.

Your task is to refactor a Laravel Controller into a clean architecture while preserving exactly the same business logic.

You are an expert in:

- Laravel 12
- Repository Pattern
- Service Layer
- SOLID Principle
- Dependency Injection
- MVC Architecture
- Clean Architecture
- Clean Code
- Design Patterns

Your responsibilities:

1. Preserve ALL existing business logic.
2. Preserve ALL existing application behaviour.
3. Improve maintainability.
4. Improve reusability.
5. Reduce controller responsibility.
6. Follow Laravel Best Practices.
7. Produce production-ready code.

==================================================
IMPORTANT RULES
==================================================

- NEVER remove existing features.
- NEVER change application behaviour.
- NEVER simplify business rules.
- NEVER omit methods.
- NEVER replace implementation with comments.
- NEVER return explanations.
- NEVER use markdown.
- NEVER use ```php.
- ALWAYS generate COMPLETE PHP files.

==================================================
OUTPUT FORMAT
==================================================

For every generated file you MUST output:

===== STEP =====
<short description>

===== FILE =====
<relative/path>

<complete php file>

Example

===== STEP =====
Creating Service Layer

===== FILE =====
app/Services/GajiService.php

<?php
...

===== STEP =====
Creating Repository Interface

===== FILE =====
app/Repositories/Contracts/GajiRepositoryInterface.php

<?php
...

===== STEP =====
Creating Repository Implementation

===== FILE =====
app/Repositories/EloquentGajiRepository.php

<?php
...

===== STEP =====
Creating Form Request

===== FILE =====
app/Http/Requests/StoreGajiRequest.php

<?php
...

===== STEP =====
Registering Repository Binding

===== FILE =====
app/Providers/RepositoryServiceProvider.php

<?php
...

===== STEP =====
Updating Controller

===== FILE =====
app/Http/Controllers/Admin/GajiController.php

<?php
...

Repeat until every required file has been generated.

Output NOTHING except STEP and FILE blocks.
"""


def build_refactoring_prompt(
    source_code,
    analysis_result,
    user_feedback=""
):

    return f"""
{REFACTORING_SYSTEM_PROMPT}

==================================================
ANALYSIS RESULT
==================================================

{analysis_result}

==================================================
ORIGINAL CONTROLLER
==================================================

{source_code}

==================================================
USER FEEDBACK
==================================================

{user_feedback if user_feedback else "No additional feedback."}

==================================================
REFACTORING OBJECTIVES
==================================================

1. Reduce controller responsibility.
2. Move business logic into Service Layer.
3. Move database access into Repository Layer.
4. Apply Dependency Injection.
5. Apply SOLID Principle.
6. Improve Maintainability Index.
7. Improve Reusability Index.
8. Reduce Cyclomatic Complexity.
9. Reduce duplicated code.
10. Preserve every existing endpoint.
11. Preserve every validation rule.
12. Preserve every route.
13. Preserve every response.
14. Preserve every redirect.
15. Preserve every database transaction.
16. Preserve every query result.
17. Generate production-ready Laravel code.

==================================================
REMINDER
==================================================

Every file MUST begin with:

===== STEP =====

followed by

===== FILE =====

Do NOT explain anything.

Do NOT write markdown.

Output only STEP and FILE sections.
"""


def build_judge_prompt(before_code, after_code, original_prompt=None):
    prompt_section = ""
    if original_prompt:
        prompt_section = f"""
=== PROMPT ASLI YANG DIGUNAKAN UNTUK REFACTORING ===

{original_prompt}

"""

    return f"""
Evaluasilah hasil refactoring berikut.
{prompt_section}
=== KODE SEBELUM REFACTORING ===

{before_code}

=== KODE SESUDAH REFACTORING ===

{after_code}

=== KRITERIA PENILAIAN ===

1. Prompting
Menilai apakah instruksi/prompt yang digunakan dalam proses refactoring
mampu menghasilkan output yang sesuai dengan tujuan refactoring.

2. Pemisahan tanggung jawab
Menilai apakah tanggung jawab controller, logika bisnis, dan akses data
telah dipisahkan dengan baik.

3. Modularitas
Menilai apakah kode telah dibagi menjadi komponen yang memiliki tanggung
jawab spesifik dan dapat dikembangkan secara terpisah.

4. Keterbacaan kode
Menilai kemudahan kode untuk dipahami berdasarkan struktur, penamaan,
dan organisasi kode.

5. Penerapan SOLID
Menilai kesesuaian struktur hasil refactoring dengan prinsip SOLID
yang relevan.

6. Penerapan arsitektur
Menilai kesesuaian penerapan Service Layer dan Repository Pattern.

=== TUGAS ===

Berikan skor 1-5 untuk setiap kriteria.

Berikan alasan untuk setiap skor.

Kemudian hitung nilai rata-rata seluruh kriteria.

Jangan memberikan skor berdasarkan asumsi. Gunakan hanya bukti yang
dapat ditemukan pada kode.

Khusus kriteria Prompting, apabila prompt asli tidak diberikan,
nyatakan bahwa penilaian terhadap kriteria tersebut memiliki keterbatasan.
Apabila prompt asli diberikan, nilai kriteria ini berdasarkan sejauh mana
kode hasil refactoring sesuai dengan instruksi pada prompt tersebut.

=== FORMAT OUTPUT WAJIB ===

Tulis terlebih dahulu narasi evaluasi lengkap seperti biasa (penjelasan
per kriteria beserta alasannya).

Setelah seluruh narasi selesai, di baris paling akhir, tambahkan blok
skor terstruktur PERSIS dengan format berikut, tanpa markdown code
fence (tanpa ```), tanpa teks tambahan setelah blok ini:

===== SCORES_JSON =====
{{
  "prompting": <angka 1-5>,
  "responsibility_separation": <angka 1-5>,
  "modularity": <angka 1-5>,
  "readability": <angka 1-5>,
  "solid": <angka 1-5>,
  "architecture": <angka 1-5>
}}

Pastikan blok ini adalah JSON valid (gunakan tanda kutip ganda,
tanpa komentar, tanpa trailing comma) dan nilainya konsisten dengan
skor yang telah dijelaskan pada narasi di atas.
"""