</main>

<script>
// ==========================
// HAMBURGER MENU
// ==========================
const hamburger = document.getElementById('hamburger');
const sidebar = document.querySelector('.sidebar');

if(hamburger && sidebar){
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        
        // Animation du hamburger (optionnel)
        // hamburger.classList.toggle('toggle');
    });

    // Fermer sidebar si on clique en dehors (sur mobile)
    document.addEventListener('click', (e) => {
        if(window.innerWidth <= 992 && 
           sidebar.classList.contains('show') && 
           !sidebar.contains(e.target) && 
           !hamburger.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
}

// ==========================
// PROFILE FORM AJAX (si présent sur la page)
// ==========================
const profileForm = document.querySelector('.profile-container form');
if(profileForm){
    profileForm.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert('Profil mis à jour avec succès');
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    });
}
</script>
</body>
</html>