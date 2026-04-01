@extends('parent.layouts.parentlayout')

@section('title', __('Communication'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Communication') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Send messages and feedback to administrators and teachers') }}</p>
        </div>
    </div>

    <!-- Contact Information Card -->
    <div class="bg-gradient-to-r from-amber-600 to-amber-700 dark:from-amber-800 dark:to-amber-900 rounded-xl shadow-lg p-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-lg mb-3">
                    <i class="bi bi-envelope text-xl"></i>
                </div>
                <h3 class="font-semibold mb-1">{{ __('Email Support') }}</h3>
                <p class="text-amber-100 text-sm">We respond within 24 hours</p>
            </div>
            <div>
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-lg mb-3">
                    <i class="bi bi-chat-dots text-xl"></i>
                </div>
                <h3 class="font-semibold mb-1">{{ __('Direct Contact') }}</h3>
                <p class="text-amber-100 text-sm">Reach out to your child's teachers</p>
            </div>
            <div>
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-lg mb-3">
                    <i class="bi bi-telephone text-xl"></i>
                </div>
                <h3 class="font-semibold mb-1">{{ __('Phone Support') }}</h3>
                <p class="text-amber-100 text-sm">Available during office hours</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Message Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Send Message') }}</h2>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-lg">
                        <p class="text-red-700 dark:text-red-300 font-semibold">{{ __('Please fix the following errors:') }}</p>
                        <ul class="list-disc list-inside text-red-700 dark:text-red-300 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 rounded-lg">
                        <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('parent.communication.send') }}" class="space-y-6">
                    @csrf

                    <!-- Recipient Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Send to') }}</label>
                        <select name="recipient_type" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
                            <option value="">{{ __('Select recipient type') }}</option>
                            <option value="admin">{{ __('Administration') }}</option>
                            <option value="teacher">{{ __('Teacher/Instructor') }}</option>
                        </select>
                        @error('recipient_type')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Subject') }}</label>
                        <input type="text" name="subject" required value="{{ old('subject') }}" placeholder="{{ __('Enter subject line') }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
                        @error('subject')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Message') }}</label>
                        <textarea name="message" required rows="8" placeholder="{{ __('Write your message here...') }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600 resize-none">{{ old('message') }}</textarea>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">{{ __('Maximum 5000 characters') }}</p>
                        @error('message')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                            <i class="bi bi-send"></i>
                            {{ __('Send Message') }}
                        </button>
                        <button type="reset" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition">
                            {{ __('Clear') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar: Quick Contact Suggestions -->
        <div class="space-y-6">
            <!-- Frequently Asked Topics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Common Topics') }}</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('Attendance Issues') }}</a></li>
                    <li><a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('Academic Performance') }}</a></li>
                    <li><a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('Course Registration') }}</a></li>
                    <li><a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('Fee Related Queries') }}</a></li>
                    <li><a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('General Inquiry') }}</a></li>
                </ul>
            </div>

            <!-- Tips -->
            <div class="bg-blue-50 dark:bg-blue-900 rounded-xl border border-blue-200 dark:border-blue-700 p-6">
                <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-4 flex items-center gap-2">
                    <i class="bi bi-lightbulb"></i>
                    {{ __('Tips') }}
                </h3>
                <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                    <li class="flex gap-2">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ __('Be clear and specific about your concern') }}</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ __('Include relevant details (child name, course, etc.)') }}</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ __('Check notices for answers to common questions') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
