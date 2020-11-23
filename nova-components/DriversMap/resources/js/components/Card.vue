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
window.Vue = require('vue');
  // Parse Here
const Parse = require('parse');
Parse.initialize("REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV", "VSDqMVaQWg5HDnFM0oAezLdeDRdfMvdZKhgW7THn");
Parse.serverURL = "https://smartaxi.b4a.io";

var Client = new Parse.LiveQueryClient({
    applicationId: 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
    serverURL: 'wss://' + 'smartaxi.b4a.io', // Example: 'wss://livequerytutorial.back4app.io'
    javascriptKey: 'VSDqMVaQWg5HDnFM0oAezLdeDRdfMvdZKhgW7THn'
});
 const query = new Parse.Query("Stream");
query.equalTo("model", "Driver");
Client.open();
var subscription = Client.subscribe(query);
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
        this.listen();
        this.getDrivers();


    },
    methods: {
        getDrivers(){
            axios.get('/api/drivers')
            .then(res => {
                this.drivers=res.data;
                res.data.forEach(item=>{
                    var element={}
                    element.position={lat:item.lat,lng:item.lng}
                    element.icon=(item.busy==1)?'/images/car-active.png':'/images/car-deactive.png';
                    element.id=item.id;
                    this.markers.push(element);
                });
            });
        },
        listen(){


                subscription.on("create", (feedDoc) => {
                    let index = this.markers.findIndex(
                    (o) => o.id === feedDoc.attributes.pid
                    );

                    if(index>-1){
                        axios.get('/api/drivers/'+feedDoc.attributes.pid)
                        .then((res) => {
                            var element={}
                            element.position={lat:res.data.lat,lng:res.data.lng}
                            element.icon=(res.data.busy==1)?'/images/car-active.png':'/images/car-deactive.png';
                            Vue.set(this.markers, index, element);
                        });

                    }



                });


        },
    },

    mounted() {
        //
    },
}
</script>
