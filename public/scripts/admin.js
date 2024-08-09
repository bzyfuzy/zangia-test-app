function openModal() {
  document.getElementById("addExamModal").style.display = "flex";
}

function removeOption(e) {
  e.parentElement.parentElement.remove();
}
function validateAndUpload(input) {
  var URL = window.URL || window.webkitURL;
  var file = input.files[0];

  if (file) {
    var image = new Image();

    image.onload = function () {
      if (this.width) {
        document.querySelector(".file-field").style.display = "none";
        const img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        document.querySelector(".thumb-preview").appendChild(img);
        console.log("Image has width, I think it is real image");
      }
    };

    image.src = URL.createObjectURL(file);
  }
}
document.addEventListener("DOMContentLoaded", () => {
  let pageNum = 1;
  let isLoading = false;
  let haveNext = true;
  const container = document.querySelector(".main-wrapper");
  const loadingContainer = document.querySelector(".loading");
  const resultContainer = document.querySelector(".exam-list");

  function fetchData() {
    if (isLoading) return;
    isLoading = true;
    loadingContainer.style.display = "flex";
    fetch(`/api/exams?page=${pageNum}`)
      .then((res) => {
        res
          .json()
          .then((finalRes) => {
            if (finalRes.length == 0) {
              haveNext = false;
            }
            finalRes.forEach((item) => {
              const resultDiv = document.createElement("div");
              resultDiv.classList.add("exam-container");
              resultDiv.innerHTML = `<div class="status-cont">
                  <div>
                    <div></div>
                  </div>
                  <button></button>
                </div>
                <img src="${item.thumbnail}" alt="exam-thumb" />
                <p>${item.title}</p>
                <div><button class="test" onclick="location.href='/admin/exam/${item.id}'">
                  Засах
                </button>
                </div>
                `;
              resultContainer.appendChild(resultDiv);
            });
            isLoading = false;
            loadingContainer.style.display = "none";
          })
          .catch((e) => {
            isLoading = false;
            loadingContainer.style.display = "none";
          });
      })
      .catch((err) => {
        isLoading = false;
        loadingContainer.style.display = "none";
      });
  }

  window.onscroll = function (ev) {
    if (
      window.innerHeight + window.pageYOffset >=
      resultContainer.scrollHeight
    ) {
      if (haveNext) {
        pageNum++;
        fetchData();
      }
    }
  };
  fetchData();

  let question = 0;
  const questionsContainer = document.getElementById("questionsContainer");
  const addQuestionButton = document.getElementById("add_question");

  document.querySelector("span.close").addEventListener("click", () => {
    document.getElementById("addExamModal").style.display = "none";
  });

  addQuestionButton.addEventListener("click", () => {
    question += 1;
    const newQuestion = document.createElement("div");
    newQuestion.classList.add("question");
    newQuestion.innerHTML = `<p class="q-spacer">Асуулт ${question + 1}</p>
                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын гарчиг
                                        </label>
                                        <input type="text" name="questions[${question}][question_title]" placeholder="Асуултын гарчиг"/>
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын текст
                                        </label>
                                        <input type="text" name="questions[${question}][question_text]" placeholder="Асуултын текст" required />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын зурагны зам /URL/
                                        </label>
                                        <input type="text" name="questions[${question}][question_image]" placeholder="Асуултын зураг" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын бичлэгний зам /URL/
                                        </label>
                                        <input type="text" name="questions[${question}][question_video]" placeholder="Асуултын бичлэг" />
                                    </div>
                                    <p>Хариултууд:</p>
                                    <div class="answersContainer" style="display:flex; flex-direction:column;gap:10px">
                                        <input type="number" name="question_id" value="${question}" hidden disabled />
                                        <div class="text-field">
                                            <label for="title">
                                                Зөв хариулт
                                            </label>
                                            <input type="text" name="questions[${question}][correct_answer]" placeholder="Зөв хариулт" required />
                                        </div>

                                        <div class="text-field">
                                            <label for="title">
                                                Хариултын сонголт
                                            </label>
                                            <input type="text" name="questions[${question}][answers][]" placeholder="Хариулт" required />
                                        </div>
                                    </div>
                                    <button type="button" class="add_answer">Сонголт нэмэх</button>
`;
    questionsContainer.appendChild(newQuestion);
  });

  document.addEventListener("click", (event) => {
    if (event.target && event.target.classList.contains("add_answer")) {
      const answersContainer = event.target.previousElementSibling;
      let question_id;
      try {
        question_id = Number(
          answersContainer
            .getElementsByTagName("input")
            .namedItem("question_id").value
        );
      } catch (e) {
        question_id = 0;
      }

      const newAnswer = document.createElement("div");
      newAnswer.className = "text-field";
      newAnswer.innerHTML = `<label for="title">Хариултын сонголт</label>
        <div style="display:flex; gap:1rem;">
        <input
          type="text"
          style="flex:1;"
          name="questions[${question_id}][answers][]"
          placeholder="Хариулт"
          required
        />
        <button type="button" onclick="removeOption(this)" style="margin: 0;padding: 0px 10px;border-radius: 5px; background-color:#ff5252;">Хасах</button>
        </div>`;
      answersContainer.appendChild(newAnswer);
    }
  });
});
