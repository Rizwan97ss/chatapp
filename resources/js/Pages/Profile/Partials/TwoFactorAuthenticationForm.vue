<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();

const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(() => {
    return !enabling.value && page.props.auth.user?.two_factor_enabled;
});

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: 'confirmTwoFactorAuthentication',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios.post(route('two-factor.recovery-codes')).then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
            recoveryCodes.value = [];
        },
        onFinish: () => {
            disabling.value = false;
        },
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-slate-950 dark:text-white">
                Two Factor Authentication
            </span>
        </template>

        <template #description>
            <span class="text-slate-500 dark:text-slate-400">
                Add extra security to your account using an authenticator app.
            </span>
        </template>

        <template #content>
            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:ring-slate-700">
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl dark:bg-indigo-950/40">
                        🛡️
                    </div>

                    <div>
                        <h3 class="text-base font-black text-slate-950 dark:text-white">
                            <template v-if="twoFactorEnabled && !confirming">
                                Two factor authentication is enabled.
                            </template>

                            <template v-else-if="twoFactorEnabled && confirming">
                                Finish enabling two factor authentication.
                            </template>

                            <template v-else>
                                Two factor authentication is not enabled.
                            </template>
                        </h3>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                            When enabled, you will be asked for a secure one-time code during login.
                            You can get this code from apps like Google Authenticator, Microsoft Authenticator, or Authy.
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="twoFactorEnabled" class="mt-6 space-y-6">
                <div
                    v-if="qrCode"
                    class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <h4 class="text-sm font-black text-slate-950 dark:text-white">
                        Authenticator setup
                    </h4>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                        <template v-if="confirming">
                            Scan this QR code with your authenticator app, or enter the setup key manually.
                            Then enter the generated code below to complete setup.
                        </template>

                        <template v-else>
                            Two factor authentication is enabled. You can scan this QR code or use the setup key if you need to configure another authenticator app.
                        </template>
                    </p>

                    <div class="mt-5 inline-block rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200" v-html="qrCode" />

                    <div
                        v-if="setupKey"
                        class="mt-5 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
                    >
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Setup key
                        </p>

                        <p class="mt-2 break-all font-mono text-sm font-bold text-slate-700 dark:text-slate-200" v-html="setupKey" />
                    </div>

                    <div v-if="confirming" class="mt-5">
                        <label for="code" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            Authentication code
                        </label>

                        <input
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            placeholder="Enter 6-digit code"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-lg font-black tracking-[0.35em] text-slate-700 placeholder:text-sm placeholder:font-semibold placeholder:tracking-normal placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />

                        <p v-if="confirmationForm.errors.code" class="mt-2 text-sm font-medium text-rose-600">
                            {{ confirmationForm.errors.code }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="recoveryCodes.length > 0 && !confirming"
                    class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-xl dark:bg-emerald-950/40">
                            🔑
                        </div>

                        <div>
                            <h4 class="text-sm font-black text-slate-950 dark:text-white">
                                Recovery codes
                            </h4>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Store these recovery codes in a secure password manager. They can be used to recover access if your authenticator device is lost.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-2 rounded-3xl bg-slate-950 p-5 font-mono text-sm font-semibold text-white sm:grid-cols-2">
                        <div
                            v-for="code in recoveryCodes"
                            :key="code"
                            class="rounded-2xl bg-white/10 px-4 py-3"
                        >
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div v-if="!twoFactorEnabled">
                    <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                            :disabled="enabling"
                        >
                            <span v-if="enabling">Enabling...</span>
                            <span v-else>Enable Two Factor</span>
                        </button>
                    </ConfirmsPassword>
                </div>

                <div v-else class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                        <button
                            v-if="confirming"
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none"
                            :disabled="enabling || confirmationForm.processing"
                        >
                            <span v-if="confirmationForm.processing">Confirming...</span>
                            <span v-else>Confirm</span>
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <button
                            v-if="recoveryCodes.length > 0 && !confirming"
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Regenerate Recovery Codes
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <button
                            v-if="recoveryCodes.length === 0 && !confirming"
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Show Recovery Codes
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <button
                            v-if="confirming"
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            :disabled="disabling"
                        >
                            Cancel
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <button
                            v-if="!confirming"
                            type="button"
                            class="rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-100 transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50 dark:shadow-none"
                            :disabled="disabling"
                        >
                            <span v-if="disabling">Disabling...</span>
                            <span v-else>Disable</span>
                        </button>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>