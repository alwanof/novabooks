<template>
<div>
    <div class="m-4">
        <button type="button" @click="createOrder" class="btn btn-default btn-primary">create order</button>
        <button type="button" @click="updateOrder" class="btn btn-default btn-primary">update order</button>
        <button type="button" @click="deleteOrder" class="btn btn-default btn-primary">delete order</button>
    </div>
          <card class="flex flex-col items-center justify-center">
        <div class="px-3 py-3">
            <h1 class="text-center text-3xl text-80 font-light">
                Orders Card
                <button type="button" v-show="card.authUser.settings.auto_fwd_order==1"  class="btn btn-default btn-danger">Auto-Forward</button>
            </h1>
        </div>

            <table class="table w-full table-default">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Customer</th>
                        <th>From/TO</th>
                        <th>Info</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody v-for="order in orders" :key="order.id">
                    <tr class="text-center">
                        <td>[{{(order.driver)?'>>':'o'}}] {{order.id}}</td>
                        <td>
                            {{order.name}}<br>
                            {{order.email}} {{order.phone}}
                            </td>
                        <td>
                            {{order.from_address}}<br>
                            {{order.to_address}}<br>
                            <span v-if="order.to_lat>0 && order.to_lng>0 && order.from_lat>0 && order.from_lng>0">
                                {{distance(order.from_lat,order.from_lng,order.to_lat,order.to_lng,'K')}}KM
                            </span><br>
                            {{order.offer}}

                        </td>
                        <td>
                            {{(order.driver)?order.driver.name:''}}<br>
                            {{statusLabel(order.status)}}
                        </td>
                        <td >
                            <span class="m-2" v-if="order.status==0">
                                <button class="btn btn-default" @click="reject(order)"><i class="far fa-window-close"></i> </button>
                            </span>
                            <span class="m-2" v-if="order.status==0 && card.authUser.settings.offer_enabled==0">

                                <button class="btn btn-default" @click="approve(order)"><i class="far fa-check-circle"></i></button>
                            </span>
                            <span class="m-2" v-if="order.status==0 && card.authUser.settings.offer_enabled==1 && order.to_address!=null">
                                <input type="number" placeholder="Ex. 50" v-model="offer" class="w-50 form-control form-input form-input-bordered">
                                <button class="btn btn-default btn-primary mr-4" @click="sendOffer(order)">Send</button>
                            </span>

                            <span class="m-2" v-if="order.status==1">
                                <select name="driver" v-model="driver" class="w-50 form-control form-input form-input-bordered">
                                    <option value="0" selected disabled>Select Driver</option>
                                    <option v-for="driver in order.drivers" :key="driver.id" :value="driver.id">{{driver.name}}({{driver.distance}}km)</option>

                                </select>
                                <button class="btn btn-default btn-primary mr-4" @click="selectDriver(order)">Apply</button>
                            </span>

                        </td>


                    </tr>

                </tbody>
            </table>



    </card>

</div>

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
export default {
    name: "TaxiOrderCard",
    props: [
        'card',

        // The following props are only available on resource detail cards...
        // 'resource',
        // 'resourceId',
        // 'resourceName',
    ],
 data() {
        return {
            orders:[],
            offer:0,
            driver:0

        }
    },
    created() {
        this.listen("Order");
        // axios.get('/api/testo/'+this.card.authUser.id).then((res) => {
        //         console.log(res.data);
        //     });

        console.log(this.card.authUser.settings.auto_fwd_order);


    },
    methods: {
        reject(order){
            axios.get('/api/order/office/reject/'+order.id).then((res) => {
                console.log(res.data);
            });

        },
        approve(order){
            axios.get('/api/order/office/approve/'+order.id).then((res) => {
                console.log(res.data);
            });

        },
        sendOffer(order){
             axios.get('/api/order/office/send/'+this.offer+'/to/'+order.id).then((res) => {
                //console.log(res.data);
            });

        },
        selectDriver(order){
            axios.get('/api/order/office/select/'+this.driver+'/to/'+order.id).then((res) => {
                //console.log(res.data);
            });

        },


        createOrder(){
            axios.post('/api/orders/create',
                {
                    uid:this.card.authUser.id,
                    session:this.makeid(6),
                    name:this.makeid(4)+' '+this.makeid(3),
                    email:this.makeid(5)+'@'+this.makeid(4)+'.com',
                    phone:'90'+this.makeint(10),
                    from_address:this.makeid(5),
                    from_lat:41.986543,
                    from_lng:28.986543,
                }
            ).then((res) => {
                //this.parseBroadcast({pid:res.data.id,model:'Order',action:'C'});
            });


        },
        updateOrder(){
             axios.post('/api/orders/update',{title:'some title 3',desc:'some desc 3'}).then((res) => {
                //this.parseBroadcast({pid:res.data.id,model:'Order',action:'C'});
            });
        },
        deleteOrder(){
             axios.post('/api/orders/delete',{title:'some title 3',desc:'some desc 3'}).then((res) => {
                //this.parseBroadcast({pid:res.data.id,model:'Order',action:'C'});
            });
        },
        getOrders(){
        axios.get('/api/orders/'+this.card.authUser.id).then((res) => {
                this.orders=res.data;
            });
        },
        parseBroadcast({pid:pid,model:model,action:action},meta=null){
            const Stream = Parse.Object.extend('Stream');
            const myNewObject = new Stream();

            myNewObject.set('pid', pid);
            myNewObject.set('model', model);
            myNewObject.set('action', action);
            myNewObject.set('meta', meta);

            myNewObject.save().then(
                (result) => {
                    console.log('Success Broadcast')
                },
                (error) => {
                    if (typeof document !== 'undefined') document.write(`Error while creating Stream: ${JSON.stringify(error)}`);
                    console.error('Error while creating Stream: ', error);
                }
            );

        },
        listen(){

                const query = new Parse.Query("Stream");
                query.equalTo("model", "Order");
                Client.open();
                var subscription = Client.subscribe(query);
                subscription.on("create", (feedDoc) => {
                    let index = this.orders.findIndex(
                    (o) => o.id === feedDoc.attributes.pid
                    );


                    //if (index > -1) {
                    axios
                        .get( '/api/orders/get/' + feedDoc.attributes.pid)
                        .then((res) => {
                        if (feedDoc.attributes.action == "U") {
                            Vue.set(this.orders, index, res.data);
                        } else if (feedDoc.attributes.action == "C") {
                            console.log('C',feedDoc.attributes.action);
                            this.orders.unshift(res.data);
                        } else if (feedDoc.attributes.action == "D") {
                            this.orders.splice(index, 1);
                        }
                        });

                });


        },
        makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        },
        makeint(length) {
            var result           = '';
            var characters       = '0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        },
        statusLabel(status){
            var label='-';
            switch (status) {
                case 0:
                    label='New';
                    break;
                case 1:
                    label='Accepted';
                    break;
                case 2:
                    label='Waiting Driver Approve';
                    break;
                case 21:
                    label='Proccessing..';
                    break;
                case 3:
                    label='Waiting Customer Approve';
                    break;
                case 9:
                    label='Done';
                    break;
                case 91:
                    label='Office Reject';
                    break;
                case 92:
                    label='Customer Reject';
                    break;
                case 93:
                    $label='No-Resp from Office';
                    break;
                case 94:
                    label='No-Resp from Customer';
                    break;

                default:
                    break;
            }
            return label;
        },
        distance(lat1, lon1, lat2, lon2, unit) {
            if ((lat1 == lat2) && (lon1 == lon2)) {
                return 0;
            }
            else {
                var radlat1 = Math.PI * lat1/180;
                var radlat2 = Math.PI * lat2/180;
                var theta = lon1-lon2;
                var radtheta = Math.PI * theta/180;
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                if (dist > 1) {
                    dist = 1;
                }
                dist = Math.acos(dist);
                dist = dist * 180/Math.PI;
                dist = dist * 60 * 1.1515;
                if (unit=="K") { dist = dist * 1.609344 }
                if (unit=="N") { dist = dist * 0.8684 }
                return Math.round(dist*100)/100;
            }
        }
    },

    mounted() {
        //const users = Parse.Object.extend("User");
        //const query = new Parse.Query(users);
        this.getOrders();

    },
}
</script>
