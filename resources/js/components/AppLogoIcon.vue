<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 28 28"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M20 12a6 6 0 1 1-12 0 6 6 0 0 1 12 0zm-6 8c-5.33 0-10 2.67-10 6v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4c0-3.33-4.67-6-10-6z"
        />
    </svg>
</template>
