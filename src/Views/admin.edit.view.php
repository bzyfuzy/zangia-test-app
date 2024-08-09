<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шалгалтын систем - Нэврэх</title>
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <link rel="stylesheet" type="text/css" href="/css/home.css">
    <link rel="stylesheet" type="text/css" href="/css/modal.css">
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
    <style>
        button {
            color: white;
            background: var(--dodger-400);
            padding: 10px 0px;
            margin: 10px 0px;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0px;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;

        }
    </style>
    </style>

    <script>
        function validateAndUpload(input) {
            var URL = window.URL || window.webkitURL;
            var file = input.files[0];

            if (file) {
                var image = new Image();

                image.onload = function() {
                    if (this.width) {
                        document.querySelector(".file-field").style.display = "none";
                        const img = document.createElement("img");
                        img.src = URL.createObjectURL(file);
                        document.querySelector(".thumb-preview").appendChild(img);
                        console.log('Image has width, I think it is real image');
                    }
                };

                image.src = URL.createObjectURL(file);
            }
        };
    </script>
</head>

<body>
    <div style="display: flex;">
        <div>
            <?php if (is_array($reports_result)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Хэрэглэгчийн ID</th>
                            <th>Нийт асуулт</th>
                            <th>Зөв хариулсан</th>
                            <th>Зарцуулсан хугацаа</th>
                        </tr>
                    </thead>
                    <?php foreach ($reports_result as $key => $value): ?>
                        <tr>
                            <td><?= $value['user_id']; ?></td>
                            <td><?= $value['sumQuestions']; ?></td>
                            <td><?= $value['corrected']; ?></td>
                            <td><?= $value['completed_seconds']; ?> сек</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        <div class="add-container">
            <div class="form-cont">
                <form method="post" enctype="multipart/form-data" id="addExamForm">
                    <div class="text-field">
                        <label for="title">
                            Хичээлийн нэр
                        </label>
                        <input type="text" name="title" id="title" placeholder="" class="" value="<?php echo $exam_data['exam']['title'] ?>" />
                    </div>

                    <div class="text-field">
                        <label for="description">
                            Хичээлийн талаарх тайлбар
                        </label>
                        <input type="text" name="description" id="description" placeholder="" class="" value="<?php echo $exam_data['exam']['description'] ?>" />
                    </div>

                    <div class="file-field-cont">
                        <label>
                            Хичээлийн зураг
                        </label>

                        <div class="file-field">
                            <input type="file" name="exam_image" id="file" class="sr-only" accept="image/*" onChange="validateAndUpload(this);" />
                            <label for="file">
                                <div>
                                    <span class="desc" style="color: #6B7280;">
                                        Хичээлүүлийн зургаа сонгоно уу! 1x1
                                    </span>
                                    <span
                                        class="text-btn">
                                        Зураг сонгох
                                    </span>
                                </div>
                            </label>
                        </div>
                        <div class="thumb-preview"></div>
                    </div>

                    <div class="questions">
                        <p>Асуултууд:</p>
                        <div id="questionsContainer" style="display:flex; flex-direction:column;gap:20px">

                            <?php foreach ($exam_data['questions'] as $key => $value): ?>
                                <div class="question">
                                    <p class="q-spacer">Асуулт <?php echo $key + 1 ?></p>
                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын гарчиг
                                        </label>
                                        <input type="text" name="questions[0][question_title]" placeholder="Асуултын гарчиг" value="<?php echo $value["question"]['title'] ?>" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын текст
                                        </label>
                                        <input type="text" name="questions[0][question_text]" placeholder="Асуултын текст" required value="<?php echo $value["question"]['text'] ?>" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын зурагны зам /URL/
                                        </label>
                                        <input type="text" name="questions[0][question_image]" placeholder="Асуултын текст" value="<?php echo $value["question"]['image'] ?>" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын бичлэгний зам /URL/
                                        </label>
                                        <input type="text" name="questions[0][question_video]" placeholder="Асуултын бичлэг" value="<?php echo $value["question"]['video'] ?>" />
                                    </div>
                                    <p>Хариултууд:</p>
                                    <div class="answersContainer" style="display:flex; flex-direction:column;gap:10px">
                                        <input type="number" name="question_id" value="<?php echo $key + 1 ?>" hidden disabled />
                                        <?php foreach ($value["answers"] as $key => $answer): ?>
                                            <div class="text-field">
                                                <label for="title">
                                                    <?php echo $answer['is_correct'] ? "Зөв хариулт" : "Хариултын сонголт" ?>
                                                </label>
                                                <input type="text" name="questions[0][answers][]" placeholder="Хариулт" required value="<?php echo $answer['text'] ?>" />
                                            </div>
                                        <?php endforeach; ?>
                                        <!-- <div class="text-field">
                                            <label for="title">
                                                Зөв хариулт
                                            </label>
                                            <input type="text" name="questions[0][correct_answer]" placeholder="Зөв хариулт" required />
                                        </div>

                                        <div class="text-field">
                                            <label for="title">
                                                Хариултын сонголт
                                            </label>
                                            <input type="text" name="questions[0][answers][]" placeholder="Хариулт" required />
                                        </div> -->
                                    </div>
                                    <button type="button" class="add_answer">Сонголт нэмэх</button>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>



                    <button type="button" id="add_question" class="upload-button">Шинэ асуулт нэмэх</button>

                    <button type="submit" disabled
                        class="upload-button">
                        Хадгалах
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>