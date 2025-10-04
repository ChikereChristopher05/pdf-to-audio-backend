import sys
import os
import PyPDF2
from gtts import gTTS

def extract_text_from_pdf(pdf_path, start=1, end=0):
    """Extract text from a PDF file, optionally between start and end pages."""
    text = ""
    try:
        with open(pdf_path, "rb") as f:
            reader = PyPDF2.PdfReader(f)
            total_pages = len(reader.pages)

            if end == 0 or end > total_pages:
                end = total_pages

            if start < 1:
                start = 1
            if end < start:
                end = start

            for i in range(start - 1, end):
                page_text = reader.pages[i].extract_text()
                if page_text:
                    text += page_text + "\n"
    except Exception as e:
        print(f"❌ Error reading PDF: {e}")
        sys.exit(1)

    return text.strip()

def main():
    if len(sys.argv) < 3:
        print("Usage: python convert.py input.pdf output.mp3 [start] [end] [lang]")
        sys.exit(1)

    pdf_file = sys.argv[1]
    mp3_file = sys.argv[2]
    start = int(sys.argv[3]) if len(sys.argv) > 3 else 1
    end = int(sys.argv[4]) if len(sys.argv) > 4 else 0
    lang = sys.argv[5] if len(sys.argv) > 5 else "en"

    if not os.path.exists(pdf_file):
        print(f"❌ PDF file not found: {pdf_file}")
        sys.exit(1)

    # Extract text
    text = extract_text_from_pdf(pdf_file, start, end)
    if not text:
        print("❌ No text could be extracted from the PDF.")
        sys.exit(1)

    # Convert to speech
    try:
        tts = gTTS(text=text, lang=lang, slow=False)
        tts.save(mp3_file)
        print(f"✅ Conversion successful: {mp3_file}")
    except Exception as e:
        print(f"❌ Error generating MP3: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
