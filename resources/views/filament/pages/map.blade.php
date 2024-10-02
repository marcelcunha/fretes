<x-filament-panels::page>
    <div class="flex gap-4 flex-1" x-data="app" x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('leaflet'))]" x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('leaflet'))]">

            <div id="map" class="h-full w-full bg-red-500 -z-10" wire:ignore></div>


        <div class="w-72">
            <form  >
                <div>{{ $this->form }}</div>
                <x-filament::button wire:click='findCoordinates'  class="mt-4">
                    Buscar
                </x-filament::button>
            </form>

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
                latitude: @entangle('latitude').live,
                longitude: @entangle('longitude').live,
                range: @entangle('range').defer,
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

                    this.$watch('latitude', () => {
                        this.plotCircle();
                    });

                },
                async plotCircle() {
                    this.loading = true;
                    this.circle?.remove();

                    try {

                        this.circle = L.circle([this.latitude, this.longitude], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: 100* 1000,
                            zIndexOffset: 1
                        }).addTo(this.map);
                    } catch (e) {
                        console.log(e);
                    } finally {
                        this.loading = false;
                    }
                },
                // async getCoordinates() {

                //     const params = new URLSearchParams({
                //         address: this.address,
                //         num: this.num,
                //         state: this.state,
                //         city: this.city,
                //         cep: this.cep
                //     });

                //     data = await (await fetch(`api/coordenadas?${params}`, {
                //         method: 'GET',
                //     })).json();

                //     return data;
                // }
            }
        }
    </script>
</x-filament-panels::page>
