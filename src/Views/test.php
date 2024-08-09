<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шалгалтын систем - Нэврэх</title>
    <style>
        * {
            box-sizing: border-box;
        }

        .add-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-cont {
            margin: 0 auto;
            width: 100%;
            max-width: 550px;
            background: white;
        }

        .form-cont form {
            padding: 1rem 2.25rem;
        }

        .form-cont .text-field {
            margin-bottom: 1.25rem;
        }

        .text-field label {
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #07074D;
        }

        .text-field input {
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #e0e0e0;
            background-color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            color: #6B7280;
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .text-field input:focus {
            border-color: #6A64F1;
        }

        .file-field-cont {
            margin-bottom: 1.25rem;
            padding-top: 1rem;
        }

        .file-field-cont label {
            margin-bottom: 1.25rem;
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #07074D;
        }

        .file-field {
            margin-bottom: 2rem;
        }

        .file-field label {
            position: relative;
            display: flex;
            min-height: 200px;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            border: 1px dashed #e0e0e0;
            padding: 3rem;
            text-align: center;
            margin: 0;
        }

        .file-field .desc {
            margin-bottom: 0.5rem;
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #07074D;
        }

        .file-field .text-btn {
            display: inline-flex;
            border-radius: 0.25rem;
            border: 1px solid #e0e0e0;
            padding: 0.5rem 1.75rem;
            font-size: 1rem;
            font-weight: 500;
            color: #07074D;
        }

        .file-field input[type='file'] {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .upload-button {
            width: 100%;
            border-radius: 0.375rem;
            background-color: #6A64F1;
            padding: 0.75rem 2rem;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .upload-button:hover {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .thumb-preview img {
            width: 450px;
            height: 450px;
            margin: 0 auto;
        }

        .thumb-preview {
            display: flex;
            justify-items: center;
        }

        .questions {
            display: flex;
            flex-direction: column;
            border: 1px solid #07074D;
            padding: 0rem 0.75rem;
        }

        .questions p {
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #07074D;
        }

        .question {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .question .text-field label {
            margin-bottom: 0.25rem;
            display: block;
            font-size: 1rem;
            color: #07074D;
        }

        .question .text-field input {
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #e0e0e0;
            background-color: white;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            color: #6B7280;
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .question .text-field {
            margin-bottom: 0;
        }
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
                            <div class="text-field">
                                <label for="title">
                                    Асуултын гарчиг
                                </label>
                                <input type="text" name="questions[0][question_title]" placeholder="Асуултын гарчиг" required />
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
                                <input type="file" name="questions[0][question_image]" placeholder="Асуултын текст" required />
                            </div>

                            <div class="text-field">
                                <label for="title">
                                    Асуултын бичлэгний зам /URL/
                                </label>
                                <input type="file" name="questions[0][question_video]" placeholder="Асуултын текст" required />
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



                <button type="button" id="add_question">Асуулт нэмэх</button>
                <button type="submit">Хадгалах</button>



                <div>
                    <button type="submit"
                        class="upload-button">
                        Хадгалах
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>