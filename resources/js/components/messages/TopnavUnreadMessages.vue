<template>
    <div id="topnav-unread-messages__wrapper">
        <a :href="href" id="topnav-unread-messages" :class="{ active: mutableCount > 0 }">
            <span id="topnav-unread-messages__icon">
                <i class="fas fa-envelope"></i>
            </span>
            <span id="topnav-unread-messages__count">
                {{ mutableCount }}
            </span>
        </a>
    </div>
</template>

<script>
    export default {
        props: [
            "count",
            "href",
        ],
        data: () => ({
            tag: "[topnav-unread-messages]",
            mutableCount: 0,
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" href: ", this.href);
                console.log(this.tag+" count: ", this.count);
                this.initializeData();
            },
            initializeData() {
                this.mutableCount = this.count;
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #topnav-unread-messages__wrapper {
        #topnav-unread-messages {
            display: flex;
            color: #000000;
            padding: 6px 10px;
            border-radius: 3px;
            flex-direction: row;
            align-items: center;
            transition: all .3s;
            text-decoration: none;
            box-sizing: border-box;
            background-color: hsl(0, 0%, 95%);
            &.active {
                color: #fff;
                background-color: #f84343;
                &:hover {
                    background-color: #df1212;
                }
            }
            &:hover {
                background-color: hsl(0, 0%, 90%);
            }
            #topnav-unread-messages__icon {
                font-size: 13px;
            }
            #topnav-unread-messages__count {
                font-size: .8em;
                margin: 0 0 0 5px;
            }
        }
    }
</style>