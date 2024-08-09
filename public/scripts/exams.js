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
              <button class="test" onclick="location.href='/exam/${item.id}'">
                Шалгат өгөх
              </button>`;
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
});
