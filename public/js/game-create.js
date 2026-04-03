// tworzenie/edycja gry - dynamiczne pola
(function() {
    var form = document.querySelector('.game-create-form');
    if (!form) return;

    // punkty
    var sectionPoints = form.querySelector('.game-create-section-points');
    var btnAddPoint = sectionPoints && sectionPoints.querySelector('.game-create-btn-add-point');
    var checkpointsList = sectionPoints && sectionPoints.querySelector('.game-create-checkpoints-list');
    
    var existingPoints = sectionPoints ? sectionPoints.querySelectorAll('.game-create-checkpoint') : [];
    var pointIndex = existingPoints.length;

    function removePointRow(ev) {
        var checkpoint = ev.target.closest('.game-create-checkpoint');
        if (checkpoint) checkpoint.remove();
    }

    if (btnAddPoint && checkpointsList && sectionPoints) {
        sectionPoints.querySelectorAll('.game-create-remove-point').forEach(function(btn) {
            btn.addEventListener('click', removePointRow);
        });
        
        btnAddPoint.addEventListener('click', function() {
            var punktowi = [];
            try {
                var raw = sectionPoints.getAttribute('data-punktowi');
                if (raw) punktowi = JSON.parse(raw);
            } catch (e) {}
            
            var selectOpts = '<option value="">-- Wybierz punktowego --</option>';
            punktowi.forEach(function(p) {
                selectOpts += '<option value="' + (p.id || '') + '">' + (p.name || '') + ' (' + (p.email || '') + ')</option>';
            });
            
            var row = document.createElement('div');
            row.className = 'game-create-checkpoint';
            row.innerHTML =
                '<input type="text" class="game-create-input game-create-input-inline" name="points[' + pointIndex + '][name]" placeholder="np. Strażnica">' +
                '<select class="game-create-input game-create-input-inline game-create-select-punktowy" name="points[' + pointIndex + '][user_id]" aria-label="Wybierz punktowego">' + selectOpts + '</select>' +
                '<input type="number" class="game-create-input game-create-input-inline game-create-input-points" value="10" min="0" name="points[' + pointIndex + '][points]">' +
                '<span class="iconify game-create-icon-trash game-create-remove-point" data-icon="mdi:delete-outline" data-width="20" data-height="20" title="Usuń"></span>';
            checkpointsList.appendChild(row);
            row.querySelector('.game-create-remove-point').addEventListener('click', removePointRow);
            pointIndex++;
        });
    }

    // zastepy
    var teamsContainer = form.querySelector('#teams-container');
    var btnAddTeam = form.querySelector('.game-create-btn-add-team');
    var teamCardEmpty = form.querySelector('#team-card-empty');
    var teamTemplate = form.querySelector('#team-card-template');
    
    var existingTeams = teamsContainer ? teamsContainer.querySelectorAll('.game-create-team-card:not(.game-create-team-card--empty)') : [];
    var teamIndex = existingTeams.length;

    function updateTeamCount(card) {
        var list = card.querySelector('[data-user-list]');
        var badge = card.querySelector('.game-create-team-count');
        if (list && badge) {
            var n = list.querySelectorAll('li').length;
            badge.textContent = n + ' ' + (n === 1 ? 'osoba' : 'osób');
        }
    }

    function removeParticipant(ev) {
        var li = ev.target.closest('li');
        if (li) {
            var card = li.closest('.game-create-team-card');
            li.remove();
            if (card) updateTeamCount(card);
        }
    }

    function addParticipantClick(ev) {
        var card = ev.target.closest('.game-create-team-card');
        if (!card || card.classList.contains('game-create-team-card--empty')) return;
        var select = card.querySelector('[data-user-select]');
        if (!select) return;
        if (select.style.display === 'none' || !select.style.display) {
            select.style.display = 'block';
            select.focus();
        } else {
            select.style.display = 'none';
        }
    }

    function onParticipantSelect(ev) {
        var select = ev.target;
        var value = select.value;
        if (!value) return;
        var card = select.closest('.game-create-team-card');
        var teamIndexAttr = card.getAttribute('data-team-index');
        var list = card.querySelector('[data-user-list]');
        var option = select.options[select.selectedIndex];
        var label = option.textContent;
        
        var li = document.createElement('li');
        li.innerHTML = '<span class="game-create-participant-name">' + label + '</span> <span class="iconify game-create-remove-participant" data-icon="mdi:close" data-width="16" data-height="16" title="Usuń"></span>';
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'teams[' + teamIndexAttr + '][user_ids][]';
        hidden.value = value;
        li.appendChild(hidden);
        list.appendChild(li);
        select.value = '';
        select.style.display = 'none';
        updateTeamCount(card);
        li.querySelector('.game-create-remove-participant').addEventListener('click', removeParticipant);
    }

    // init dla istniejacych kart
    form.querySelectorAll('.game-create-team-card:not(.game-create-team-card--empty)').forEach(function(card) {
        var btnAdd = card.querySelector('[data-add-participant]');
        var select = card.querySelector('[data-user-select]');
        if (btnAdd) btnAdd.addEventListener('click', addParticipantClick);
        if (select) {
            select.style.display = 'none';
            select.addEventListener('change', onParticipantSelect);
        }
        card.querySelectorAll('.game-create-remove-participant').forEach(function(btn) {
            btn.addEventListener('click', removeParticipant);
        });
    });

    if (btnAddTeam && teamCardEmpty && teamTemplate && teamsContainer) {
        btnAddTeam.addEventListener('click', function() {
            var clone = teamTemplate.content.cloneNode(true);
            var card = clone.querySelector('.game-create-team-card');
            card.setAttribute('data-team-index', teamIndex);
            var nameInput = card.querySelector('.game-create-team-name');
            if (nameInput) nameInput.name = 'teams[' + teamIndex + '][name]';
            teamsContainer.insertBefore(clone, teamCardEmpty);
            var newCard = teamsContainer.querySelector('.game-create-team-card[data-team-index="' + teamIndex + '"]');
            var btnAdd = newCard.querySelector('[data-add-participant]');
            var select = newCard.querySelector('[data-user-select]');
            if (btnAdd) btnAdd.addEventListener('click', addParticipantClick);
            if (select) {
                select.style.display = 'none';
                select.addEventListener('change', onParticipantSelect);
            }
            teamIndex++;
        });
    }
})();
