<template>
    <card class="flex flex-col items-center justify-center">

        <GmapMap
              ref="map"
              :center="center"
              :zoom="10"
              :draggable="true"
              style="width: 100%; height: 400px" >
              <GmapMarker
                :clickable="true"
                v-for="marker in markers"
                :key="marker.id"
                :name="marker.name"
                :icon="marker.icon"
                :position="marker.position" >

              </GmapMarker>
            </GmapMap>
    </card>
</template>

<script>
export default {
     name: "DriverMap",
    props: [
        'card'

        // The following props are only available on resource detail cards...
        // 'resource',
        // 'resourceId',
        // 'resourceName',
    ],
    data() {
        return {
            drivers:[],
            title:'foo',
            center: {
                lat: 41.1374382,
                lng: 28.7547977,
            },
            markers: []
        }
    },
    created() {
        //console.log(this.card.currentVisitors);

        axios.get('http://nova.local/api/drivers')
        .then(res => {
            this.drivers=res.data;
            res.data.forEach(item=>{
                var element={}
                element.position={lat:item.lat,lng:item.lng}
                element.icon='http://nova.local/images/car-active.png'
                this.markers.push(element);
            });
        });
    },

    mounted() {
        //
    },
}
</script>
