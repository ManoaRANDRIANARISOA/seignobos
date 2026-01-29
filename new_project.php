<div class="form-container">
    <h2>Nouveau projet</h2>
    
    <form method="POST" id="projectForm">
        <label>Nom du projet</label>
        <input type="text" name="project_name" placeholder="Nom du projet" required>

        <label>Description du projet</label>
        <textarea name="project_description" rows="3" placeholder="Description du projet"></textarea>

        <div id="fieldsContainer"></div>

        <button type="button" onclick="addField()"><i class="fas fa-plus"></i> Ajouter un champ</button>
        <input type="hidden" name="form_data" id="form_data">
        <button type="submit"><i class="fas fa-save"></i> Sauvegarder le projet</button>
    </form>
</div>

<style>
.form-container {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: auto;
}
.form-container h2 { margin-bottom: 25px; text-align: center; color: #333; }
.form-container label { display: block; margin: 15px 0 5px; font-weight: 600; color: #555; }
.form-container input, .form-container textarea, .form-container select {
    width: 100%; padding: 10px; margin-bottom: 5px; border-radius: 6px; border: 1px solid #ddd; font-family: 'Poppins', sans-serif;
}
.form-container input:focus, .form-container textarea:focus, .form-container select:focus {
    outline: none; border-color: #f5a623; box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.1);
}
.form-container button {
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    color: #fff; font-weight: 600; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;
    transition: 0.3s; margin-top: 15px; font-family: 'Poppins', sans-serif;
}
.form-container button:hover { filter: brightness(1.1); transform: translateY(-1px); }
.field-card { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; position: relative; border: 1px solid #eee; }
.field-card .remove-field { position: absolute; top: 15px; right: 15px; cursor: pointer; color: #e74c3c; font-weight: bold; font-size: 18px; transition: 0.2s; }
.field-card .remove-field:hover { transform: scale(1.1); }
.options-container { margin-top: 15px; padding: 15px; background: #fff; border-radius: 8px; border: 1px solid #eee; display: none; }
.options-list .option-item { display: flex; gap: 10px; margin-top: 10px; align-items: center; }
.options-list input { flex: 1; padding: 8px; }
.options-list button { flex: 0 0 auto; background: #ff4757; color: #fff; border-radius: 6px; width: 30px; height: 30px; line-height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; }
.add-option { background: #2ed573 !important; margin-top: 10px; }
</style>

<script>
let fieldCount = 0;

function addField() {
    fieldCount++;
    const container = document.getElementById('fieldsContainer');
    const fieldCard = document.createElement('div');
    fieldCard.className = 'field-card';
    fieldCard.innerHTML = `
        <span class="remove-field" onclick="this.parentNode.remove()" title="Supprimer le champ"><i class="fas fa-times"></i></span>
        <label>Nom du champ</label><input type="text" class="field-name" placeholder="Ex: Date de naissance" required>
        <label>Type de champ</label>
        <select class="field-type" onchange="updateOptions(this)">
            <option value="text">Texte court</option>
            <option value="number">Nombre</option>
            <option value="textarea">Texte long</option>
            <option value="file">Fichier</option>
            <option value="select">Liste déroulante</option>
            <option value="checkbox">Cases à cocher</option>
            <option value="radio">Boutons radio</option>
        </select>
        <div class="options-container">
            <label>Options :</label>
            <div class="options-list"></div>
            <button type="button" class="add-option" onclick="addOption(this)"><i class="fas fa-plus"></i> Ajouter une option</button>
        </div>
        <label style="margin-top:15px; display:flex; align-items:center; gap:10px; cursor:pointer;">
            <input type="checkbox" class="enable-ai" checked style="width:auto; margin:0;"> 
            <span>Autoriser le remplissage par IA</span>
        </label>
    `;
    container.appendChild(fieldCard);
}


function updateOptions(selectEl) {
    const fieldCard = selectEl.closest('.field-card');
    const optionsContainer = fieldCard.querySelector('.options-container');
    if(['select','checkbox','radio'].includes(selectEl.value)) optionsContainer.style.display='block';
    else { optionsContainer.style.display='none'; fieldCard.querySelector('.options-list').innerHTML=''; }
}

function addOption(btn) {
    const optionsList = btn.parentNode.querySelector('.options-list');
    const div = document.createElement('div');
    div.className = 'option-item';
    div.innerHTML = `<input type="text" placeholder="Nom de l'option"><button type="button" onclick="this.parentNode.remove()">×</button>`;
    optionsList.appendChild(div);
}

function prepareFormData() {
    const fields = document.querySelectorAll('.field-card');
    const formData = [];
    fields.forEach(f => {
        const name = f.querySelector('.field-name').value.trim();
        const type = f.querySelector('.field-type').value;
        const optionsInputs = f.querySelectorAll('.options-list input');
        const options = [];
        optionsInputs.forEach(o => { if(o.value.trim() !== '') options.push(o.value.trim()); });
        if(['select','checkbox','radio'].includes(type) && !options.includes('Autre')) options.push('Autre');
        const allowAI = f.querySelector('.enable-ai').checked;
        formData.push({name,type,options,allowOther:true,allowAI});
    });
    document.getElementById('form_data').value = JSON.stringify(formData);
}

// Gestion de la soumission AJAX
document.getElementById('projectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    prepareFormData();
    
    const formData = new FormData(this);
    
    fetch('create_project_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            // Optionnel : réinitialiser le formulaire
            document.getElementById('projectForm').reset();
            document.getElementById('fieldsContainer').innerHTML = '';
            fieldCount = 0;
            // Ne pas recharger la page, rester sur l'onglet
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la création du projet.');
    });
});
</script>
