<x-home.layout :title="'Simas MTTG - Detail Masjid'">
	<x-home._navbar />
	<x-home.mosque._hero/>
	<x-home.mosque._nav_tab/>
	<div class="container my-4">
		@include('home.mosque.detail._profile')

		<div class="mosque-main">
			@include('home.mosque.detail._gallery')
			@include('home.mosque.detail._bkm_information')
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
							try{ history.pushState(null, '', '#'+tab); }catch(e){}
							const target = document.getElementById(tab);
							if(target){ target.scrollIntoView({behavior:'smooth', block:'start'}); }
							// update layout offset
							updateLayoutForProfile();
						});
					});

					// Update layout offset based on profile visibility
					function updateLayoutForProfile(){
						const profile = document.getElementById('profile');
						const main = document.querySelector('.mosque-main');
						if(!profile || !main) return;
						if(profile.classList.contains('show')){
							// keep profile visible; CSS adjacency selector will shift .mosque-main
							profile.style.display = '';
						}else{
							profile.style.display = 'none';
						}
					}

					const init = (location.hash||'#profile').replace('#','') || 'profile';
					setActive(init);
					updateLayoutForProfile();
					window.addEventListener('hashchange', ()=>{
						const h = (location.hash||'#profile').replace('#','');
						setActive(h);
						const target = document.getElementById(h);
						if(target){ target.scrollIntoView({behavior:'smooth', block:'start'}); }
						updateLayoutForProfile();
					});

				})();
			</script>
		</div>
	</div>
</x-home.layout>