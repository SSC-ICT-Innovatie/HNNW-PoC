<template>
    <div id="dashboard-my-tasks">

        <div id="my-tasks__list" v-if="mutableTasks.length > 0">
            <a class="my-task" v-for="(task, ti) in mutableTasks" :key="ti" :href="task.view_href">
                <span class="my-task__title">{{ task.title }}</span>
                <span class="my-task__status">{{ task.status.label }}</span>
                <!-- <div class="my-task__" -->
            </a>
        </div>

        <div id="my-tasks__empty" v-if="mutableTasks.length === 0">
            {{ noRecordsText }}
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "tasks",
            "noRecordsText",
        ],
        data: () => ({
            tag: "[dashboard-my-tasks]",
            mutableTasks: [],
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" tasks: ", this.tasks);
                // console.log(this.tag+" ");
                this.initializeData();
            },
            initializeData() {
                if (this.tasks !== undefined && this.tasks !== null && this.tasks.length > 0) {
                    for (let i = 0; i < this.tasks.length; i++) {
                        this.mutableTasks.push(this.tasks[i]);
                    }
                }
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #dashboard-my-tasks {
        #my-tasks__list {
            .my-task {
                display: flex;
                color: #000;
                padding: 10px 15px;
                flex-direction: row;
                align-items: center;
                transition: all .3s;
                text-decoration: none;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                &:last-child {
                    border-bottom: 0;
                }
                &:hover {
                    background-color: hsl(0, 0%, 98%);
                }
                .my-task__title {
                    flex: 1;
                }
                .my-task__status {

                }
            }
        }
        #my-tasks__empty {
            padding: 15px;
            text-align: center;
            box-sizing: border-box;
        }
    }
</style>