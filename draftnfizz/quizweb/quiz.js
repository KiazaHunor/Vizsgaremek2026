let selectedAnswer = null;
let currentQuestionId = null;

function loadQuestion() {
  fetch("http://localhost:3000/api/quiz")
    .then(response => response.json())
    .then(data => {
      currentQuestionId = data.id;
      document.getElementById("question").innerText = data.question;

      const answersDiv = document.getElementById("answers");
      answersDiv.innerHTML = "";

      data.answers.forEach(answer => {
        const btn = document.createElement("button");
        btn.innerText = answer;

        btn.onclick = function () {
          selectedAnswer = answer;
        };

        answersDiv.appendChild(btn);
      });
    })
    .catch(error => {
      console.error("Hiba:", error);
    });
}

function submitAnswer() {
  if (!selectedAnswer) {
    alert("Válassz egy választ!");
    return;
  }

  fetch("http://localhost:3000/api/quiz/answer", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      questionId: currentQuestionId,
      answer: selectedAnswer
    })
  })
    .then(response => response.json())
    .then(data => {
      document.getElementById("result").innerText =
        data.correct ? "✅ Helyes!" : "❌ Rossz!";
    })
    .catch(error => {
      console.error("Hiba:", error);
    });
}

loadQuestion();