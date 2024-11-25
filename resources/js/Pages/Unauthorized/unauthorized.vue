<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div
            class="text-center bg-white p-8 rounded-lg shadow-lg max-w-md w-full"
        >
            <div class="flex justify-center">
                <img
                    src="/resources/css/mdc.png"
                    alt="Logo"
                    class="rounded-full shadow-md w-16 h-16"
                />
            </div>

            <h1 class="text-3xl font-bold text-red-600 mb-4">
                Unauthorized Access
            </h1>
            <p class="text-lg text-gray-600">
                You do not have permission to access this page.
            </p>
            <div class="mt-6">
                <a
                    href="/dashboard"
                    class="text-blue-500 cursor-pointer hover:underline"
                >
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Unauthorized",
};
</script>
