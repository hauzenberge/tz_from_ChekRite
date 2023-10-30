<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps(['user']);

const tasks = ref([]);
const newTaskTitle = ref('');
const newTaskDescription = ref('');

const editMode = ref(false);
const editedTask = ref({});

const getTasks = () => {
    axios.get(`/api/users/${props.user.id}/tasks`)
        .then(response => {
            tasks.value = response.data;
        })
        .catch(error => {
            console.error(error);
        });
};

const createTask = () => {
    axios.post('/api/tasks', {
        title: newTaskTitle.value,
        description: newTaskDescription.value,
        user_id: props.user.id
    })
        .then(response => {
            newTaskTitle.value = '';
            newTaskDescription.value = '';
            tasks.value.push(response.data);
        })
        .catch(error => {
            console.error(error);
        });
};

const editTask = (task) => {
    editMode.value = true;
    editedTask.value = { ...task };
};

const saveTask = () => {
    axios.put(`/api/tasks/${editedTask.value.id}`, {
        title: editedTask.value.title,
        description: editedTask.value.description,
        status: editedTask.value.status
    })
        .then(response => {
            const index = tasks.value.findIndex(task => task.id === response.data.id);
            if (index !== -1) {
                tasks.value[index] = response.data;
            }
            editMode.value = false;
        })
        .catch(error => {
            console.error(error);
        });
};

const deleteTask = (taskId) => {
    axios.delete(`/api/tasks/${taskId}`)
        .then(() => {
            tasks.value = tasks.value.filter(task => task.id !== taskId);
        })
        .catch(error => {
            console.error(error);
        });
};

const statusClass = (status) => {
    switch (status) {
        case 'в роботі':
            return 'text-yellow-700';
        case 'виконано':
            return 'text-green-600';
        case 'невідомо':
            return 'text-red-600';
        default:
            return 'text-gray-600';
    }
};

onMounted(() => {
    getTasks();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="createTask">
                        <div class="mb-2">
                            <input v-model="newTaskTitle" type="text" class="rounded-md border border-gray-400 p-2"
                                placeholder="Title" required>
                        </div>
                        <div class="mb-2">
                            <textarea v-model="newTaskDescription" class="rounded-md border border-gray-400 p-2"
                                placeholder="Description" required></textarea>
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white rounded-md py-2 px-4">
                                <i class="fas fa-plus mr-2"></i>Create Task
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <table class="w-full border-collapse border rounded-lg">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="p-2">ID</th>
                                    <th class="p-2">Title</th>
                                    <th class="p-2">Description</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Edit/Save</th>
                                    <th class="p-2">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="task in tasks" :key="task.id" class="bg-white">
                                    <td class="p-2">{{ task.id }}</td>
                                    <td class="p-2">
                                        <input v-model="editedTask.title" v-if="editMode && editedTask.id === task.id"
                                            type="text" class="rounded-md border border-gray-400 p-2 w-full">
                                        <span v-else>{{ task.title }}</span>
                                    </td>
                                    <td class="p-2">
                                        <textarea v-model="editedTask.description"
                                            v-if="editMode && editedTask.id === task.id"
                                            class="rounded-md border border-gray-400 p-2 w-full"></textarea>
                                        <span v-else>{{ task.description }}</span>
                                    </td>
                                    <td class="p-2">
                                        <select v-model="editedTask.status" v-if="editMode && editedTask.id === task.id"
                                            class="rounded-md border border-gray-400 p-2">
                                            <option value="невідомо">невідомо</option>
                                            <option value="в роботі">в роботі</option>
                                            <option value="виконано">виконано</option>
                                        </select>
                                        <span v-else :class="statusClass(task.status)">{{ task.status }}</span>
                                    </td>
                                    <td class="p-2">
                                        <button @click="editTask(task)" v-if="!editMode"
                                            class="bg-green-500 hover:bg-green-700 text-white rounded-md py-1 px-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button @click="saveTask" v-if="editMode && editedTask.id === task.id"
                                            class="bg-blue-500 hover:bg-blue-700 text-white rounded-md py-1 px-2">
                                            <i class="fas fa-save"></i> Save
                                        </button>

                                    </td>
                                    <td class="p-2">
                                        <button @click="deleteTask(task.id)" v-if="task.status === 'виконано' && !editMode"
                                            class="bg-red-500 hover:bg-red-700 text-white rounded-md py-1 px-2">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>