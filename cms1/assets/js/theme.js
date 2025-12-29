// Завантаження теми при старті
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('blog_theme') || 'blue';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Виділити активний колір в picker
    const activeOption = document.querySelector(`.color-option[data-theme="${savedTheme}"]`);
    if (activeOption) {
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
        activeOption.classList.add('active');
    }
});

// Зміна теми
function changeTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('blog_theme', theme);
    
    // Оновити активний стан
    document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
    document.querySelector(`.color-option[data-theme="${theme}"]`).classList.add('active');
}
