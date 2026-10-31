import os
from dotenv import load_dotenv
from openai import OpenAI

load_dotenv()

API_KEY = os.getenv("OPENAI_API_KEY")
MODEL = "gpt-5.6-sol"

if not API_KEY:
    raise ValueError("OPENAI_API_KEY belum diatur di file .env")

client = OpenAI(api_key=API_KEY)

def ask_llm(system_prompt, user_prompt, temperature=0.2):
    response = client.chat.completions.create(
        model=MODEL,
        messages=[
            {
                "role": "system",
                "content": system_prompt
            },
            {
                "role": "user",
                "content": user_prompt
            }
        ]
    )

    return response.choices[0].message.content