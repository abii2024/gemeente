<x-guest-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Bedankt!</h1>
                    @if (session('complaint_id'))
                        <p class="text-lg text-gray-600 mb-6">
                            Uw klacht is succesvol ingediend en heeft nummer <strong>#{{ session('complaint_id') }}</strong>.
                        </p>
                    @else
                        <p class="text-lg text-gray-600 mb-6">
                            Uw klacht is succesvol ingediend.
                        </p>
                    @endif
                    <p class="text-gray-600 mb-8">
                        We nemen uw melding serieus en zullen deze zo spoedig mogelijk in behandeling nemen. 
                        U ontvangt een bevestiging op het opgegeven e-mailadres.
                    </p>
                    
                    <div class="space-y-4">
                        <a href="{{ route('home') }}" 
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            Terug naar Home
                        </a>
                        <br>
                        <a href="{{ route('complaint.create') }}" 
                           class="inline-block text-blue-600 hover:text-blue-800 underline">
                            Nieuwe klacht indienen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
