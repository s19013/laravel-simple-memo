function deleteHandle(event) {
    // フォームの動きを止めることができる
    event.preventDefault();
    if (window.confirm('本当に削除しても良い?')) {
        document.getElementById('delete-form').submit();
    } else {
        alert('キャンセルしました');
    }
}
