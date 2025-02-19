document.addEventListener('DOMContentLoaded', () => {
    // Provera da li je trenutna stranica prava (prema ID-u ili klasi)
    if (window.location.pathname === '/products/') { 
        // Inicijalno učitavanje svih proizvoda
        getProducts();

        // Event listener za selektovanje tipa produkta
        document.getElementById('select_tip-produkta').addEventListener('change', (e) => {
            const tipProduktta = e.target.value;
            const nazivProduktta = document.getElementById('search_naziv-produkta').value;
            getProducts(tipProduktta, nazivProduktta);

        });

        // Event listener za pretragu po nazivu produkta
        document.getElementById('search_naziv-produkta').addEventListener('input', (e) => {
            const nazivProduktta = e.target.value;
            const tipProduktta = document.getElementById('select_tip-produkta').value;
            getProducts(tipProduktta, nazivProduktta);
        });

		// Event listener za paginaciju
		document.getElementById('product-pg-container').addEventListener('click', function(e) {
			if (e.target.classList.contains('pagination_btn')) {
				const nazivProduktta = document.getElementById('search_naziv-produkta').value;
				const tipProduktta = document.getElementById('select_tip-produkta').value;
				const currentPage = Number(e.target.innerText);
				console.log(currentPage)
				// Poziv funkcije koja ponovo crta paginaciju
				getProducts(tipProduktta, nazivProduktta, currentPage);
		
				// Sačekaj da se DOM osveži, pa postavi stil aktivnog dugmeta
				setTimeout(() => {
					document.querySelectorAll('.pagination_btn').forEach(btn => {
						btn.classList.remove('active-page'); // Ukloni klasu sa svih
					});
		
					// Ponovo pronađi dugme sa istim brojem i dodaj mu klasu
					document.querySelectorAll('.pagination_btn').forEach(btn => {
						if (Number(btn.innerText) === currentPage) {
							btn.classList.add('active-page');
						}
					});
				}, 280); // Kratko kašnjenje da se sačeka novi render
			}
		});
		
    }
});

let total_pages = 0;

function addPagination() {

	if (total_pages > 1) {

	let pagination = ``;

		// Resetovanje sadržaja da se spreči dupliranje
	document.getElementById('product-pg-container').innerHTML = '';
	
	for (let i = 0; i < total_pages; i++) {
		let activeClass = (i === 0) ? 'active-page' : '';
		pagination += `
			<button class="btn pagination_btn ${activeClass}">${(i + 1)}</button>
		`
	}
	
	document.getElementById('product-pg-container').classList.add('d-flex')
	document.getElementById('product-pg-container').classList.add('flex-row')
	document.getElementById('product-pg-container').classList.add('justify-content-center')


		let added = document.querySelector('#product-pg-container').innerHTML = pagination;
		return added;
	}
}

function getProducts(selectVal = '', searchVal = '', paginationVal='') {

    // Formiranje URL-a sa parametrima
    let url = '/wp-json/custom-mcsr-shop/v2/produkti';
    const params = [];
    if (selectVal) params.push(`tip_produkta=${encodeURIComponent(selectVal)}`);
    if (searchVal) params.push(`search=${encodeURIComponent(searchVal)}`);
	if (paginationVal) params.push(`page=${encodeURIComponent(paginationVal)}&per_page=${20}`);
    if (params.length) url += `?${params.join('&')}`;

    // Fetch poziv
    fetch(url, {
        method: 'GET',
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
			total_pages = data.total_pages
            const produktiHtml = data.data.map(produkt => `
					<div class="card" style="width: 18rem;">
						<div class="card-body">
							<div class="d-flex flex-column justify-content-between">
								<div>
									<img class="card-img" width="300" height="300" src="${produkt.slika_produkta}" alt="${produkt.naziv}" />
									<h2 class="card-title">${produkt.naziv}</h2>
									<h6 class="card-subtitle mb-2 text-muted">${produkt.tip_produkta === 'majica' ? "Clothing" : produkt.tip_produkta === 'ostalo' ? 'Web Software' : 'Brand Systems'}</h6>
									<p class="card-text">${produkt.opis_produkta && produkt.opis_produkta !== 'undefined' ? produkt.opis_produkta : ''}</p>
									<h1>${produkt.cijena_produkta} €</h1>
								</div>
								<a href="http://mcsr-hub:8080/order-details/?narudzba=${produkt.naziv}&cijena=${produkt.cijena_produkta}&tip=${produkt.tip_produkta}" class="card-link d-block w-100 text-center btn btn-success mt-5">SEND AN ORDER</a>
							</div>
						</div>
					</div>
            `).join('');
			// <a href="http://mcsr-hub:8080/order-details/?narudzba=${produkt.naziv}&cijena=${produkt.cijena_produkta}&tip=${produkt.tip_produkta}" class="card-link d-block w-100 text-center btn btn-success mt-5">SEND AN ORDER</a>

            // Ubacivanje generisanog HTML-a u .produkti-lista element
            document.querySelector('.produkti-lista').innerHTML = produktiHtml;

			if (data.data.length === 0) {
				document.querySelector('.err_msg').style.display = 'block';
			} else {
				document.querySelector('.err_msg').style.display = 'none';
			}
		})
		.then(()=> {
			// reset radi pravilnog prikaza stranica
			document.getElementById('product-pg-container').innerHTML = '';
			if (total_pages > 1) {
				addPagination()
			}
		})
        .catch(error => {
            console.log(error);
        });
}

function setLSValues(val, element) {
	if (val !== 'null' && val && val !== '') {
		element.value = val
	}
}

// if (window.location.href === 'http://mcsr-hub/%d0%bf%d1%80%d0%be%d0%b4%d0%b0%d0%b2%d0%bd%d0%b8%d1%86%d0%b0/') {
// 	let queryParam = "?narudzba="
// 	setTimeout(()=> {
// 			let article_btn1 = document.querySelector('.naruci-1');

// 			let article1 = 'majica1';
// 			article_btn1.addEventListener('click', ()=> {
// 				window.location.replace(`http://mcsr-hub/детаљи-наруџбе/${queryParam}${article1}`)
// 			})
// 	},100)
// }

if (window.location.pathname === '/order-details/' && window.location.search !== "") {
	console.log(window.location.search)
	setTimeout(()=> {
			let nazivInput = document.getElementById('wpforms-348-field_27')

			let imeInput = document.getElementById('wpforms-348-field_1')
			let prezimeInput = document.getElementById('wpforms-348-field_1-last')
			let emailInput = document.getElementById('wpforms-348-field_24')
			let cepterInput = document.getElementById('wpforms-348-field_31')
			let adresInput = document.getElementById('wpforms-348-field_28')

			setLSValues(localStorage.getItem('ime'), imeInput)
			setLSValues(localStorage.getItem('prezime'), prezimeInput)
			setLSValues(localStorage.getItem('email'), emailInput)
			setLSValues(localStorage.getItem('cepter'), cepterInput)
			setLSValues(localStorage.getItem('adresa'), adresInput)

			imeInput.addEventListener('blur', ()=> {
				localStorage.setItem('ime', imeInput.value)
			})
			prezimeInput.addEventListener('blur', ()=> {
				localStorage.setItem('prezime', prezimeInput.value)
			})
			emailInput.addEventListener('blur', ()=> {
				localStorage.setItem('email', emailInput.value)
			})
			cepterInput.addEventListener('blur', ()=> {
				localStorage.setItem('cepter', cepterInput.value)
			})
			adresInput.addEventListener('blur', ()=> {
				localStorage.setItem('adresa', adresInput.value)
			})

            let url = window.location.search

            let params = new URLSearchParams(url);

            let narudzba = params.get("narudzba");
            let cijena = params.get("cijena");
			let tip = params.get("tip")

            nazivInput.value = `${narudzba}, ${cijena} din`

			if (tip !== 'majica') {
				document.querySelector('.size_shirt').style.display = 'none';
				document.querySelector('.color_shirt').style.display = 'none';
				document.querySelector('.address_part').style.display = 'none';
			}
			
		},100)
} else if (window.location.pathname === '/%D0%B4%D0%B5%D1%82%D0%B0%D1%99%D0%B8-%D0%BD%D0%B0%D1%80%D1%83%D1%9F%D0%B1%D0%B5/' && window.location.search === "") {
	alert('Page does not exist')
	window.location.replace('http://mcsr-hub/')
}


