document.getElementById("uploadForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);

  try {
    const response = await fetch(
      "https://pdf-to-audio-backend.onrender.com/process.php",
      {
        method: "POST",
        body: formData,
      }
    );

    if (!response.ok) throw new Error("Conversion failed");

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);

    const link = document.createElement("a");
    link.href = url;
    link.download = "output.mp3";
    link.click();
  } catch (err) {
    alert(err.message);
  }
});
