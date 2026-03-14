<div x-data="aiAssistPanel()" 
     x-init="init()"
     class="fixed bottom-4 right-4 z-50"
     @keydown.escape.window="open = false">
    
    <!-- Toggle Button -->
    <button 
        @click="toggle()"
        class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        :class="{ 'rotate-0': !open, 'rotate-180': open }"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        <span class="font-medium">AI Assist</span>
    </button>

    <!-- Panel -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="absolute bottom-16 right-0 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
        @click.outside="open = false"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-3 flex items-center justify-between">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
                AI Writing Assistant
            </h3>
            <span x-text="statusBadge" class="text-xs px-2 py-1 rounded-full bg-white/20 text-white"></span>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button 
                x-for="(tab, index) in tabs" 
                :key="tab.id"
                @click="activeTab = tab.id"
                class="flex-1 px-3 py-2 text-sm font-medium transition-colors"
                :class="activeTab === tab.id ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'"
                x-text="tab.name"
            ></button>
        </div>

        <!-- Content -->
        <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
            <!-- Generate Tab -->
            <div x-show="activeTab === 'generate'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Topic</label>
                <input 
                    type="text" 
                    x-model="generate.topic"
                    placeholder="Enter your topic..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                <button 
                    @click="generateContent()"
                    :disabled="loading || !generate.topic"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Generating...' : 'Generate Content'"></span>
                </button>
            </div>

            <!-- Titles Tab -->
            <div x-show="activeTab === 'titles'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Topic</label>
                <input 
                    type="text" 
                    x-model="titles.topic"
                    placeholder="Enter your topic..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                <button 
                    @click="generateTitles()"
                    :disabled="loading || !titles.topic"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Generating...' : 'Generate Titles'"></span>
                </button>
                <div x-show="titles.result.length > 0" class="mt-3 space-y-2">
                    <template x-for="(title, index) in titles.result" :key="index">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg group">
                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300" x-text="title"></span>
                            <button @click="copyToClipboard(title)" class="opacity-0 group-hover:opacity-100 p-1 text-gray-500 hover:text-purple-600" title="Copy">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Improve Tab -->
            <div x-show="activeTab === 'improve'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Text</label>
                <textarea 
                    x-model="improve.text"
                    rows="4"
                    placeholder="Paste your text to improve..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <button 
                    @click="improveWriting()"
                    :disabled="loading || !improve.text"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Improving...' : 'Improve Writing'"></span>
                </button>
            </div>

            <!-- SEO Tab -->
            <div x-show="activeTab === 'seo'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Post Title</label>
                <input 
                    type="text" 
                    x-model="seo.title"
                    placeholder="Your post title..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent mb-3"
                >
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                <textarea 
                    x-model="seo.content"
                    rows="3"
                    placeholder="Your post content..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <button 
                    @click="generateSEO()"
                    :disabled="loading || !seo.title || !seo.content"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Generating...' : 'Generate SEO'"></span>
                </button>
            </div>

            <!-- Summarize Tab -->
            <div x-show="activeTab === 'summarize'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Text to Summarize</label>
                <textarea 
                    x-model="summarize.text"
                    rows="4"
                    placeholder="Paste your long text here..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Words</label>
                    <input 
                        type="number" 
                        x-model="summarize.maxLength"
                        min="50"
                        max="300"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>
                <button 
                    @click="summarizeContent()"
                    :disabled="loading || !summarize.text"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Summarizing...' : 'Summarize'"></span>
                </button>
            </div>

            <!-- Hashtags Tab -->
            <div x-show="activeTab === 'hashtags'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content for Hashtags</label>
                <textarea 
                    x-model="hashtags.text"
                    rows="4"
                    placeholder="Paste your content to extract hashtags..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Hashtags</label>
                    <input 
                        type="number" 
                        x-model="hashtags.count"
                        min="5"
                        max="20"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>
                <button 
                    @click="generateHashtags()"
                    :disabled="loading || !hashtags.text"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Generating...' : 'Generate Hashtags'"></span>
                </button>
                <div x-show="hashtags.result && hashtags.result.length > 0" class="mt-3 space-y-2">
                    <template x-for="(tag, index) in hashtags.result" :key="index">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg group">
                            <span class="flex-1 text-sm text-purple-600 dark:text-purple-400" x-text="tag"></span>
                            <button @click="copyToClipboard(tag)" class="opacity-0 group-hover:opacity-100 p-1 text-gray-500 hover:text-purple-600" title="Copy">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tone Tab -->
            <div x-show="activeTab === 'tone'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Text</label>
                <textarea 
                    x-model="tone.text"
                    rows="4"
                    placeholder="Enter text to change tone..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Tone</label>
                    <select 
                        x-model="tone.selectedTone"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="formal">Formal</option>
                        <option value="casual">Casual</option>
                        <option value="professional">Professional</option>
                        <option value="friendly">Friendly</option>
                        <option value="academic">Academic</option>
                    </select>
                </div>
                <button 
                    @click="changeTone()"
                    :disabled="loading || !tone.text"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Changing...' : 'Change Tone'"></span>
                </button>
            </div>

            <!-- Expand Tab -->
            <div x-show="activeTab === 'expand'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Text to Expand</label>
                <textarea 
                    x-model="expand.text"
                    rows="4"
                    placeholder="Enter brief ideas to expand..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <button 
                    @click="expandContent()"
                    :disabled="loading || !expand.text"
                    class="mt-2 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Expanding...' : 'Expand Content'"></span>
                </button>
            </div>

            <!-- Output Display -->
            <div x-show="output" x-cloak class="mt-4 p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-purple-600 dark:text-purple-400">Result</span>
                    <div class="flex gap-1">
                        <button @click="copyOutput()" class="p-1 text-gray-500 hover:text-purple-600" title="Copy">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <button @click="insertOutput()" class="p-1 text-gray-500 hover:text-purple-600" title="Insert to editor">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="output"></div>
            </div>

            <!-- Error Display -->
            <div x-show="error" x-cloak class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                <p class="text-sm text-red-600 dark:text-red-400" x-text="error"></p>
            </div>
        </div>
    </div>
</div>

<script>
function aiAssistPanel() {
    return {
        open: false,
        loading: false,
        output: '',
        error: '',
        activeTab: 'generate',
        isDemo: true,
        
        tabs: [
            { id: 'generate', name: 'Generate' },
            { id: 'titles', name: 'Titles' },
            { id: 'improve', name: 'Improve' },
            { id: 'seo', name: 'SEO' },
            { id: 'summarize', name: 'Summarize' },
            { id: 'hashtags', name: 'Hashtags' },
            { id: 'tone', name: 'Tone' },
            { id: 'expand', name: 'Expand' }
        ],
        
        generate: {
            topic: '',
            systemPrompt: ''
        },
        
        titles: {
            topic: '',
            result: []
        },
        
        improve: {
            text: ''
        },
        
        seo: {
            title: '',
            content: ''
        },
        
        summarize: {
            text: '',
            maxLength: 150
        },
        
        hashtags: {
            text: '',
            count: 10,
            result: []
        },
        
        tone: {
            text: '',
            selectedTone: 'formal'
        },
        
        expand: {
            text: ''
        },

        get statusBadge() {
            return this.isDemo ? 'Demo Mode' : 'AI Active';
        },

        async init() {
            // Skip network call - use demo mode by default for fast loading
            // User can toggle to real AI manually if needed
            this.isDemo = true;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    const firstInput = this.$el.querySelector('input[type="text"]');
                    if (firstInput) firstInput.focus();
                });
            }
        },

        async generateContent() {
            if (!this.generate.topic) return;
            await this.callAI('/ai/generate', { topic: this.generate.topic }, (data) => {
                this.output = data.content;
            });
        },

        async generateTitles() {
            if (!this.titles.topic) return;
            await this.callAI('/ai/titles', { topic: this.titles.topic, count: 5 }, (data) => {
                this.titles.result = data.titles;
                this.output = data.titles.join('\n');
            });
        },

        async improveWriting() {
            if (!this.improve.text) return;
            await this.callAI('/ai/improve', { text: this.improve.text }, (data) => {
                this.output = data.improved;
            });
        },

        async generateSEO() {
            if (!this.seo.title || !this.seo.content) return;
            await this.callAI('/ai/seo', { title: this.seo.title, content: this.seo.content }, (data) => {
                const seo = data.seo;
                this.output = `Meta Description:\n${seo.meta_description}\n\nKeywords:\n${seo.keywords.join(', ')}`;
            });
        },

        async summarizeContent() {
            if (!this.summarize.text) return;
            await this.callAI('/ai/summarize', { text: this.summarize.text, max_length: this.summarize.maxLength }, (data) => {
                this.output = data.summary;
            });
        },

        async generateHashtags() {
            if (!this.hashtags.text) return;
            await this.callAI('/ai/hashtags', { text: this.hashtags.text, count: this.hashtags.count }, (data) => {
                this.hashtags.result = data.hashtags;
                this.output = data.hashtags.join(' ');
            });
        },

        async changeTone() {
            if (!this.tone.text) return;
            await this.callAI('/ai/tone', { text: this.tone.text, tone: this.tone.selectedTone }, (data) => {
                this.output = data.result;
            });
        },

        async expandContent() {
            if (!this.expand.text) return;
            await this.callAI('/ai/expand', { text: this.expand.text }, (data) => {
                this.output = data.expanded;
            });
        },

        async callAI(url, payload, callback) {
            this.loading = true;
            this.output = '';
            this.error = '';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    callback(data);
                } else {
                    this.error = data.error || 'An error occurred';
                }
            } catch (e) {
                this.error = 'Failed to connect. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            this.showToast('Copied!');
        },

        copyOutput() {
            if (this.output) {
                navigator.clipboard.writeText(this.output);
                this.showToast('Copied to clipboard!');
            }
        },

        insertOutput() {
            if (this.output) {
                // Try to insert into the body textarea
                const bodyTextarea = document.querySelector('textarea[name="body"]');
                if (bodyTextarea) {
                    bodyTextarea.value = this.output;
                    bodyTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                    this.showToast('Inserted into editor!');
                } else {
                    this.copyOutput();
                }
            }
        },

        showToast(message) {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-24 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
