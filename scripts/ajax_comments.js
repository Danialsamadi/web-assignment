document.getElementById('commentForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const content = document.getElementById('commentContent').value;
    const post_id = document.getElementById('postId').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../server/add_comment.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.status == 200) {
            const newComment = document.createElement('div');
            newComment.classList.add('comment');
            newComment.innerHTML = `<p>${content}</p><p>by You just now</p>`;
            document.getElementById('commentsList').appendChild(newComment);
            document.getElementById('commentContent').value = '';
        } else {
            alert('Error adding comment.');
        }
    };

    xhr.send(`post_id=${post_id}&content=${content}`);
});
