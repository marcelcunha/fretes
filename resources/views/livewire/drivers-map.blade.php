<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> --}}

    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body class="h-full" x-data='app()'>

    <div class="m-20 flex justify-center">

        <div id="map" class="h-96 w-[30rem]"></div>

        <div class="w-52">
            <x-input label="Endereço" placeholder="Digite o endereço" x-model='address' />
            <x-input label="Número" placeholder="0000" x-model='num' />
            <x-input label="Estado" placeholder="Estado" x-model="state" />
            <x-input label="Cidade" placeholder="Cidade" x-model="city" />
            <x-input label="CEP" placeholder="00000-000" x-model="cep" />

            <div>
                <label for="customRange1" class="mb-2 inline-block text-neutral-700 dark:text-neutral-200">KM</label>
                <input type="range"
                    class="transparent h-[4px] w-full cursor-pointer appearance-none border-transparent bg-neutral-200 dark:bg-neutral-600"
                min="100" max="1000" x-model="range" step="50"/>
            </div>

            <button type="button" @click="await plotCircle()" :class="{ 'bg-gray-400': loading }"
                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Buscar</button>
        </div>
    </div>



    <script>
        const app = () => {
            return {
                drivers: @json($drivers),
                circle: null,
                map: null,
                address: '',
                num: '',
                state: '',
                city: '',
                cep: '',
                range: 100,
                loading: false,
                init() {
                    this.map = L.map('map').setView([-18.7239, -47.4989], 6);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(this.map);

                    Object.values(this.drivers).forEach(driver => {
                        const {
                            latitude,
                            longitude
                        } = driver.address;

                        L.marker([latitude, longitude], {
                            opacity: 0.8,
                            zIndexOffset: -1,
                            title: driver.name
                        }).addTo(this.map).bindPopup(driver.name);
                    });

                    $watch('range', () => {
                        this.plotCircle();
                    });
                },
                async plotCircle() {
                    this.loading = true;
                    this.circle?.remove();

                    try {
                        data = await this.getCoordinates();
                        this.circle = L.circle([data.latitude, data.longitude], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: range * 1000,
                            zIndexOffset: 1
                        }).addTo(this.map);
                    } catch (e) {
                        console.log(e);
                    } finally {
                        this.loading = false;
                    }
                },
                async getCoordinates() {

                    const params = new URLSearchParams({
                        address: this.address,
                        num: this.num,
                        state: this.state,
                        city: this.city,
                        cep: this.cep
                    });

                    data = await (await fetch(`api/coordenadas?${params}`, {
                        method: 'GET',
                    })).json();

                    return data;
                }
            }
        }
    </script>
</body>

</html>
