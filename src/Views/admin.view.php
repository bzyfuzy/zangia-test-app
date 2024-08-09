<!DOCTYPE html>
<html>

<head>
    <title>Шалгалтын систем</title>
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <link rel="stylesheet" type="text/css" href="/css/home.css">
    <link rel="stylesheet" type="text/css" href="/css/modal.css">
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
    <script src="/scripts/admin.js"></script>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="search-cont flex-row-center">

                <!-- <input type="text" id="search" placeholder="Хайх..." /> -->
            </div>
            <a href="/">Client </a>
            <a href="/logout">
                <i>Системээс гарах</i>
            </a>
        </div>
        <div class="exam-list">
            <div class="exam-container admin">
                <button class="test" onclick="openModal()">
                    Хичээл нэмэх
                </button>
            </div>
            <div class="exam-container loading">
                <div class="exam-loading">
                    Уншиж байна...
                </div>
            </div>

        </div>
    </div>

    <div id="addExamModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="add-container">
                <div class="form-cont">
                    <form method="post" enctype="multipart/form-data" id="addExamForm">
                        <div class="text-field">
                            <label for="title">
                                Хичээлийн нэр
                            </label>
                            <input type="text" name="title" id="title" placeholder="" class="" />
                        </div>

                        <div class="text-field">
                            <label for="description">
                                Хичээлийн талаарх тайлбар
                            </label>
                            <input type="text" name="description" id="description" placeholder="" class="" />
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
                                <div class="question">
                                    <p class="q-spacer">Асуулт 1</p>
                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын гарчиг
                                        </label>
                                        <input type="text" name="questions[0][question_title]" placeholder="Асуултын гарчиг" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын текст
                                        </label>
                                        <input type="text" name="questions[0][question_text]" placeholder="Асуултын текст" required />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын зурагны зам /URL/
                                        </label>
                                        <input type="text" name="questions[0][question_image]" placeholder="Асуултын зураг" />
                                    </div>

                                    <div class="text-field">
                                        <label for="title">
                                            Асуултын бичлэгний зам /URL/
                                        </label>
                                        <input type="text" name="questions[0][question_video]" placeholder="Асуултын бичлэг" />
                                    </div>
                                    <p>Хариултууд:</p>
                                    <div class="answersContainer" style="display:flex; flex-direction:column;gap:10px">
                                        <input type="number" name="question_id" value="0" hidden disabled />
                                        <div class="text-field">
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
                                        </div>
                                    </div>
                                    <button type="button" class="add_answer">Сонголт нэмэх</button>
                                </div>
                            </div>
                        </div>



                        <button type="button" id="add_question" class="upload-button">Шинэ асуулт нэмэх</button>

                        <button type="submit"
                            class="upload-button">
                            Хадгалах
                        </button>
                    </form>
                </div>
            </div>


            <!-- <form class="add-exam" id="addExamForm" method="POST" action="/admin/create-exam">
                <label for="title">Хичээлийн нэр:</label>
                <input type="text" id="title" name="title" required>
                <label for="description">Тайлбар:</label>
                <textarea id="description" name="description" rows="4"></textarea>
                <label for="thumbnail">Хичээлийн зураг:</label>
                <input type="text" id="thumbnail" name="thumbnail">

            </form> -->
        </div>
    </div>
</body>

</html>