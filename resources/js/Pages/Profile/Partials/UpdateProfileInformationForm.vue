<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value?.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value?.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            <span class="text-slate-950 dark:text-white">
                Profile Information
            </span>
        </template>

        <template #description>
            <span class="text-slate-500 dark:text-slate-400">
                Update your account profile information and email address.
            </span>
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="col-span-6"
            >
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:ring-slate-700">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div>
                            <img
                                v-show="!photoPreview"
                                :src="user.profile_photo_url"
                                :alt="user.name"
                                class="size-24 rounded-3xl object-cover shadow-sm ring-4 ring-white dark:ring-slate-700"
                            >

                            <span
                                v-show="photoPreview"
                                class="block size-24 rounded-3xl bg-cover bg-center bg-no-repeat shadow-sm ring-4 ring-white dark:ring-slate-700"
                                :style="'background-image: url(\'' + photoPreview + '\');'"
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-sm font-black text-slate-950 dark:text-white">
                                Profile photo
                            </h3>

                            <p class="mt-1 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Upload a clear photo for your SchoolPro account. JPG, PNG, or WEBP works best.
                            </p>

                            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="button"
                                    class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                                    @click.prevent="selectNewPhoto"
                                >
                                    Select New Photo
                                </button>

                                <button
                                    v-if="user.profile_photo_path"
                                    type="button"
                                    class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                    @click.prevent="deletePhoto"
                                >
                                    Remove Photo
                                </button>
                            </div>

                            <p v-if="form.errors.photo" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.photo }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <label for="name" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                    Name
                </label>

                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    autocomplete="name"
                    placeholder="Enter your name"
                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                />

                <p v-if="form.errors.name" class="mt-2 text-sm font-medium text-rose-600">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <label for="email" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                    Email
                </label>

                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                />

                <p v-if="form.errors.email" class="mt-2 text-sm font-medium text-rose-600">
                    {{ form.errors.email }}
                </p>

                <div
                    v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null"
                    class="mt-4 rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-100 dark:bg-amber-950/20 dark:ring-amber-900/40"
                >
                    <p class="text-sm font-semibold leading-6 text-amber-700 dark:text-amber-200">
                        Your email address is unverified.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="font-bold underline underline-offset-4 hover:text-amber-900 dark:hover:text-amber-100"
                            @click.prevent="sendEmailVerification"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <p
                        v-show="verificationLinkSent"
                        class="mt-2 text-sm font-bold text-emerald-600 dark:text-emerald-400"
                    >
                        A new verification link has been sent to your email address.
                    </p>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3 text-sm font-bold text-emerald-600">
                Saved.
            </ActionMessage>

            <button
                type="submit"
                class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Saving...</span>
                <span v-else>Save</span>
            </button>
        </template>
    </FormSection>
</template>