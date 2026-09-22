<div class="border-b border-gray-200 bg-white p-2">
    <h3 class="text-base font-semibold leading-6 text-gray-900">
        {{ __('Current Visitors') }}
    </h3>
</div>
<div class="p-2">
    <ul role="list" class="divide-y divide-gray-100">
        @foreach($sessions as $userSession)
            @if($userSession->user == null)
                <li class="py-4">
                    <div class="flex items-center gap-x-3">
                        <h3 class="flex-auto truncate text-sm font-semibold leading-6 text-gray-900">
                            Guest
                        </h3>
                        <span class="text-gray-400 text-xs">
                        {{ $userSession->getAttribute('ip_address') }}
                    </span>
                    </div>
                    <p class="mt-3 truncate text-xs text-gray-400">
                    <span class="text-gray-700">
                        {{ $userSession->getAttribute('user_agent') }}
                    </span>
                    </p>
                </li>
            @else
                <li class="py-4">
                    <div class="flex items-center gap-x-3">
                        <img src="{{ $userSession->user->profile_photo_url }}"
                             alt="" class="h-6 w-6 flex-none rounded-full bg-gray-800"
                        />
                        <h3 class="flex-auto truncate text-sm font-semibold leading-6 text-gray-900">
                            {{ $userSession->user->name }}
                        </h3>
                        <span class="text-gray-400 text-xs">
                        {{ $userSession->user->email }}
                    </span>
                        <time datetime="{{ $userSession->user->lastLoginAt() }}" class="flex-none text-xs text-gray-500">
                            {{ $userSession->user->lastLoginAt()->diffForHumans() }}
                        </time>
                    </div>
                    <p class="mt-3 truncate text-xs text-gray-400">
                    <span class="text-gray-700">
                        {{ $userSession->getAttribute('user_agent') }}
                    </span>
                    </p>
                </li>
            @endif
        @endforeach
    </ul>
</div>
