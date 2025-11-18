<div class="container my-4">
		<div class="mosque-actions">
			<a href="#profile" class="mosque-action" data-tab="profile">
				<span class="icon"><i class="bi bi-building"></i></span>
				<span class="label">Profil Masjid</span>
			</a>
			<a href="#gallery" class="mosque-action text-muted" data-tab="gallery">
				<span class="icon"><i class="bi bi-image"></i></span>
				<div>
					<div class="label">Galeri / Foto</div>
					
				</div>
			</a>
			<a href="#bkm" class="mosque-action text-muted" data-tab="bkm">
				<span class="icon"><i class="bi bi-people"></i></span>
				<div>
					<div class="label">Informasi BKM</div>
					
				</div>
			</a>
		</div>
</div>        

        <script>
			(function(){
				const actions = document.querySelectorAll('.mosque-action');
				function setActive(tab){
					actions.forEach(a=>{
						if(a.dataset.tab===tab){
							a.classList.add('active');
							a.classList.remove('text-muted');
						}else{
							a.classList.remove('active');
							if(!a.classList.contains('text-muted')) a.classList.add('text-muted');
						}
					});

					// show/hide sections
					document.querySelectorAll('.mosque-section').forEach(s=>{ s.classList.remove('show'); });
					const target = document.getElementById(tab);
					if(target && target.classList.contains('mosque-section')){
						target.classList.add('show');
					}
				}

				actions.forEach(a=>{
					a.addEventListener('click', function(e){
						e.preventDefault();
						const tab = this.dataset.tab || 'profile';
						setActive(tab);
						// update URL without causing the browser to jump
						try{ history.pushState(null, '', '#'+tab); }catch(e){}
						// smooth scroll to target if exists
						const target = document.getElementById(tab);
						if(target){
							target.scrollIntoView({behavior:'smooth', block:'start'});
						}
					});
				});

				const init = (location.hash||'#profile').replace('#','') || 'profile';
				setActive(init);
				window.addEventListener('hashchange', ()=>{
					const h = (location.hash||'#profile').replace('#','');
					setActive(h);
					const target = document.getElementById(h);
					if(target){ target.scrollIntoView({behavior:'smooth', block:'start'}); }
					});
			})();
		</script>