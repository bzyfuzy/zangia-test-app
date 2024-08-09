<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <title>Шалгалтын систем</title>
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <link rel="stylesheet" type="text/css" href="/css/exam.css">
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="title-container">
                <p id="exam-title">Хичээл</p>
            </div>
            <div class="process-bar-container">
                <div class="process-bar-background">
                    <div class="process-bar-indicator">
                        <p class="process-bar-title" id="progress-percentage">20%</p>
                    </div>
                </div>
            </div>
            <!-- <div class="action-buttons-container">
                <button id="pause-button">Зогсоох</button>
            </div> -->
            <div class="timer-container">
                <img />
                <div>
                    <p>Үлдсэн хугацаа:</p>
                    <p id="time-remaining">00:00</p>
                </div>
            </div>
        </div>
        <div class="exam">

            <div class="question-container">
                <p class="question-title">Асуултын гарчиг</p>
                <div class="question">
                    <p class="question-text">Асуултын тайлбар</p>
                </div>
            </div>
            <div class="answers-container">
                <div class="answer">
                    <div>
                        <div></div>
                        <p>Хариулт A</p>
                    </div>

                    <input type="checkbox">
                </div>
                <div class="answer">
                    <div>
                        <div></div>
                        <p>Хариулт B</p>
                    </div>

                    <input type="checkbox">
                </div>
                <div class="answer">
                    <div>
                        <div></div>
                        <p>Хариулт C</p>
                    </div>

                    <input type="checkbox">
                </div>
                <div class="answer">
                    <div>
                        <div></div>
                        <p>Хариулт D</p>
                    </div>

                    <input type="checkbox">
                </div>
            </div>
        </div>
        <div class="footer-action-buttons flex-row-center">
            <a href="/" class="back">Нүүр хуудасруу буцах</a>
            <button id="next-button">Дараах</button>
            <!-- <button id="finish-button">Дуусгах</button> -->
        </div>
    </div>

    <script>
        const exam_data = <?php echo json_encode($data) ?>;
        let user_answers = [];

        function handleChange(checkbox, currentQuestion) {
            var checkboxes = document.getElementsByClassName("answer-checkbox")
            for (let i = 0; i < checkboxes.length; i++) {
                const item = checkboxes[i];
                if (item !== checkbox) item.checked = false
            }
            if (checkbox.checked) {
                user_answers[currentQuestion] = checkbox.name;
            }

            // console.log(checkbox.checked, checkbox.name);?
            // user_answers
        }
        document.addEventListener('DOMContentLoaded', () => {
            const totalQuestions = exam_data.questions.length;
            let currentQuestion = 0;
            let remainingTime = 600;
            let duration = remainingTime - 1;
            document.getElementById("exam-title").innerText = exam_data.exam.title;
            const questionTitle = document.querySelector('.question-title');
            const question = document.querySelector('.question');
            const answers = document.querySelector('.answers-container');


            const updateProgress = () => {
                const progressPercentage = Math.min(100, (currentQuestion / totalQuestions) * 100);
                document.querySelector('.process-bar-indicator').style.width = `${progressPercentage}%`;
                document.querySelector('.process-bar-indicator').style.borderRadius = `10px`;
                document.getElementById('progress-percentage').innerText = `${progressPercentage}%`;

                question.innerHTML = "";
                questionTitle.innerText = exam_data.questions[currentQuestion].question.title;
                if (exam_data.questions[currentQuestion].question.text) {
                    let questionText = document.createElement("p");
                    questionText.className = "question-text";
                    questionText.innerText = exam_data.questions[currentQuestion].question.text;
                    question.appendChild(questionText);
                }

                if (exam_data.questions[currentQuestion].question.image) {
                    let questionImage = document.createElement("img");
                    questionImage.className = "question-img";
                    questionImage.src = exam_data.questions[currentQuestion].question.image;
                    question.appendChild(questionImage);
                }

                if (exam_data.questions[currentQuestion].question.video) {
                    let questionVideo = document.createElement("div");
                    questionVideo.className = "question-video";
                    questionVideo.innerText = exam_data.questions[currentQuestion].question.video;
                    question.appendChild(questionVideo);
                }
                answers.innerHTML = `<div class="result"><div/>`;
                let answers_raw = "";
                exam_data.questions[currentQuestion].answers.forEach(answer => {
                    answers_raw += ` <div class="answer">
                                        <p>${answer.text}</p>
                                    <input class="answer-checkbox" type="checkbox" name="${exam_data.questions[currentQuestion].question.id}_${answer.id}" onchange="handleChange(this, ${currentQuestion});">
                                  </div>`
                })
                answers.innerHTML = answers_raw;

            };

            const onEndExam = async () => {
                clearInterval(timerInterval);
                document.querySelector('.process-bar-indicator').style.width = `100%`;
                document.getElementById('progress-percentage').innerText = `100%`;
                document.getElementById('next-button').style.display = "none";
                const questionCont = document.querySelector(".exam");
                questionCont.innerHTML = `<div class="result"><div class="loading"><p>Таны оноог бодож байна түр хүлээнэ үү!</p></div></div>`;
                let result = {
                    exam_id: exam_data.exam.id,
                    questions: exam_data.questions.map(v => v.question.id),
                    answers: user_answers.map(v => v.split("_")[1]),
                    completed: duration - remainingTime
                }
                fetch("/api/exam", {
                    method: "POST",
                    body: JSON.stringify(result),
                    headers: {
                        "Content-Type": "application/json",
                    }
                }).then(res => {
                    res.json().then(fres => {
                        document.querySelector('.back').style.display = "block";
                        questionCont.innerHTML = `<div class="result"><p><p>Авсан оноо: ${fres.correct_answers}</p>Нийт авах оноо: ${totalQuestions}</p><p>Зарцуулсан хугацаа: ${(Math.floor((duration - remainingTime) / 60)).toString().padStart(2, '0')}:${((duration - remainingTime) % 60).toString().padStart(2, '0')}</p></div>`
                    }).catch(e => {
                        document.querySelector('.back').style.display = "block";
                        questionCont.innerHTML = `<div class="result"><p>Алдаа гарлаа дахин оролдоно уу!</p></div>`
                    })
                }).catch(e => {
                    document.querySelector('.back').style.display = "block";
                    questionCont.innerHTML = `<div class="result"><p>Алдаа гарлаа дахин оролдоно уу!</p></div>`
                })
            }


            const updateTimer = () => {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                document.getElementById('time-remaining').innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(timerInterval);
                    onEndExam();
                }
            };

            const nextQuestion = () => {
                if (!user_answers[currentQuestion]) {
                    alert("Заавал нэг хариул сонгоно уу!");
                } else {
                    if (currentQuestion < totalQuestions - 1) {

                        currentQuestion++;
                        updateProgress();
                    } else {
                        onEndExam();
                    }
                }

            };

            updateProgress();
            const timerInterval = setInterval(updateTimer, 1000);

            document.getElementById('next-button').addEventListener('click', nextQuestion);
            // document.getElementById('finish-button').addEventListener('click', finishExam);

            //It's been a long time since I wrote something without framework library
        });
    </script>
</body>

</html>