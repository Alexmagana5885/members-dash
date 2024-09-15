<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Form</title>
    <!-- Include Quill's CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Optional: Set a specific height for the editor */
        .quill-editor {
            height: 200px;
        }
    </style>
</head>

<body>
    <h1>Submit Text</h1>
    <form action="submit.php" method="POST">
        <label for="editor">Text:</label><br>
        <!-- Div for Quill editor -->
        <div id="editor" class="quill-editor"></div>
        <input type="hidden" id="text" name="text"> <!-- Hidden field to submit editor content -->
        <br><br>
        <input type="submit" value="Submit">
    </form>

    <!-- Include Quill's JavaScript -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Compose an epic...',
            modules: {
                toolbar: [
                    [{
                        'header': '1'
                    }, {
                        'header': '2'
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'image']
                ]
            }
        });

        // Function to update the hidden field with the editor's content before form submission
        document.querySelector('form').onsubmit = function() {
            document.querySelector('input[name="text"]').value = quill.root.innerHTML;
        };
    </script>


</body>

</html>