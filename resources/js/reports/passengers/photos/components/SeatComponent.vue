<template>
    <vue-draggable-resizable class="seat" :class="isOccupied ? 'occupied': ''"
                             :parent="true"
                             :w="position.width" :h="position.height" :x="position.left" :y="position.top"

                             :draggable="!fixed" :resizable="!fixed"

                             @activated="onActivated" @deactivated="onDeactivated"
                             @resizing="onResize" @resizestop="onResizeStop"
                             @dragging="onDrag" @dragstop="onDragStop">
        <input v-show="seat.selected && !fixed" class="input-sm form-control" placeholder="Seat:" v-model="seat.number">
        <small class="number">{{ seat.number }}</small>
        <span v-if="!fixed" class="position">
            <span>{{ seat.left | numberFormat('0.0') }}%, {{ seat.top | numberFormat('0.0') }}% | {{ seat.width | numberFormat('0.0') }}%, {{ seat.height | numberFormat('0.0') }}%</span>
        </span>
        <span class="center" :style="`left: ${position.center.left - 2}px; top: ${position.center.top - 2}px`"></span>
    </vue-draggable-resizable>
</template>

<script>
    import VueDraggableResizable from 'vue-draggable-resizable';
    import 'vue-draggable-resizable/dist/VueDraggableResizable.css';

    export default {
        name: "SeatComponent",
        components: {
            VueDraggableResizable
        },
        props: {
            seat: Object,
            seatingOccupied: Object | Array,
            image: Object,
            fixed: Boolean
        },
        data() {
            return {
                active: false,
                resizing: false,
                dragging: false,
            }
        },
        computed: {
            position() {
                let w = this.image.size.width * this.seat.width / 100;
                let h = this.image.size.height * this.seat.height / 100;

                if(this.fixed === true){
                    // w = w > 0 ? w : 1;
                    // h = h > 0 ? h : 1;
                }

                return {
                    left: this.image.size.width * this.seat.left / 100,
                    top: this.image.size.height * this.seat.top / 100,
                    width: w,
                    height: h,
                    center: {
                        left: w / 2,
                        top: h / 2
                    }
                };
            },
            isOccupied(){
                return this.seatingOccupied && this.seatingOccupied[this.seat.number];
            }
        },
        methods: {
            onActivated() {
                this.seat.selected = true;
            },
            onDeactivated() {
                setTimeout(() => {
                    this.seat.selected = false;
                }, 300);
            },
            onResize(x, y, w, h) {
                if (!this.fixed) {
                    this.resizing = true
                    this.setRelativePosition(x, y, w, h);
                }
            },
            onResizeStop() {
                this.resizing = false
            },
            onDrag(x, y) {
                if (!this.fixed) {
                    this.dragging = true;
                    this.setRelativePosition(x, y, null, null);
                }
            },
            onDragStop() {
                this.dragging = false;
            },
            setRelativePosition(x, y, w, h) {
                this.seat.left = 100 * (x >= 0 ? x : 0) / this.image.size.width;
                this.seat.top = 100 * (y >= 0 ? y : 0) / this.image.size.height;
                if(w !== null) this.seat.width = 100 * (w > 0 ? w : 0) / this.image.size.width;
                if(h !== null) this.seat.height = 100 * (h > 0 ? h : 0) / this.image.size.height;

                this.centerPoint();
            },
            centerPoint() {
                this.seat.center = {
                    left: this.seat.left + this.seat.width / 2,
                    top: this.seat.top + this.seat.height / 2
                };
            }
        },
        created() {
            this.centerPoint();
        }
    };
</script>

<style scoped>
    .seat {
        text-align: center;
        padding: 2px;
        background-color: rgb(0, 233, 198);
        border-radius: 3px;
        border-style: solid;
        border-color: #003941;
        transition: ease background-color 1s;
        z-index: 200 !important;
    }

    .seat.occupied {
        background-color: #a9ff01 !important;
        border-color: #000163;
    }

    .seat.active {
        background-color: rgba(13, 208, 32, 0) !important;
        box-shadow: 1px 1px 10px 1px rgba(205, 205, 205, 0.94) !important;
        border-color: #ff0051 !important;
        border-width: 2px !important;
        z-index: 10000 !important;
    }

    .seat:hover {
        cursor: move;
    }

    .seat .number {
        font-size: 1.2em !important;
        color: white;
        font-weight: bold;
        z-index: 100000;
        text-shadow: 1px 2px 3px #0600cc;
        position: relative;
        top: 35%;
    }

    .seat.occupied .number {
        color: white !important;
        text-shadow: 1px 2px 3px #400121;
    }

    .seat .center {
        position: relative;
        float: left;
        border: 1px solid #00ceff;
        height: 1px;
        width: 1px;
    }

    .seat input {
        background: #0000006e !important;
        color: white !important;
        height: 15px !important;
        font-size: 0.8em !important;
        position: absolute;
        top: -30px;
    }

    .seat .position {
        font-size: 0.7em !important;
        color: white;
        font-weight: bold;
        background: rgba(10, 21, 32, 0.71);
        padding: 2px;
        position: absolute;
        width: 120px;
        bottom: -40px;
        display: none;
    }

    .seat.active .position {
        display: block !important;
    }

    .draw {
        width: 20%;
        background: darkgrey;
        color: white;
    }
</style>