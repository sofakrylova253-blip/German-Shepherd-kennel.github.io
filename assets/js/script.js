document.addEventListener('DOMContentLoaded', function() {
    // Подтверждение удаления
    const deleteBtns = document.querySelectorAll('.confirm-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Вы уверены? Это действие необратимо.')) {
                e.preventDefault();
            }
        });
    });

    // Бронирование (глобальная функция)
    window.bookAnimal = function(animalId) {
        let name = prompt("Введите ваше имя для бронирования:");
        if (name) {
            alert("Заявка на бронирование отправлена! Мы свяжемся с вами.");
        }
    };
});
