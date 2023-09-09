@extends('master')

@section('content')
    <div class="container">
        <form action="{{ url('/configuration') }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <h2>Configuration</h2>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">

                            <div class="form-group row">
                                <label for="title" class="col-md-2 col-form-label text-md-right">Title</label>
                                <div class="col-md-6">
                                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') ?? $configurations->title }}" required autocomplete="title">
                    
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="subtitle" class="col-md-2 col-form-label text-md-right">Sub Title</label>
                                <div class="col-md-6">
                                    <input id="subtitle" type="text" class="form-control @error('subtitle') is-invalid @enderror" name="subtitle" value="{{ old('subtitle') ?? $configurations->subtitle }}" required autocomplete="subtitle">
                    
                                    @error('subtitle')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="contact" class="col-md-2 col-form-label text-md-right">Contact</label>
                                <div class="col-md-6">
                                    <input id="contact" type="text" class="form-control @error('contact') is-invalid @enderror" name="contact" value="{{ old('contact') ?? $configurations->contact }}" required autocomplete="contact">
                    
                                    @error('contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="address" class="col-md-2 col-form-label text-md-right">Address</label>
                                <div class="col-md-6">
                                    <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required autocomplete="address">{{ $configurations->address }}</textarea>
                    
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="background_image" class="col-md-2 col-form-label text-md-right">Background Image</label>
                                <div class="col-md-6">
                                    <input id="background_image" type="file" onchange="onBackgroundImageChange(event)" class="form-control @error('background_image') is-invalid @enderror" name="background_image" value="{{ old('background_image') }}" autocomplete="background_image">
                    
                                    @error('background_image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <img src="{{ url('/uploads/') . '/' . $configurations->background_image }}" id="background-image" class="img-thumbnail" width="120" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="logo" class="col-md-2 col-form-label text-md-right">Logo</label>
                                <div class="col-md-6">
                                    <input id="logo" type="file" onchange="onLogoChange(event)" class="form-control @error('logo') is-invalid @enderror" name="logo" value="{{ old('logo') }}" autocomplete="logo">
                    
                                    @error('logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <img src="{{ url('/uploads/') . '/' . $configurations->logo }}" id="logo-image" class="img-thumbnail" width="120" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="expiry_date" class="col-md-2 col-form-label text-md-right">Expiry Date</label>
                                <div class="col-md-6">
                                    <input id="expiry_date" type="date" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date" value="{{ old('expiry_date')?? $configurations->expiry_date }}" autocomplete="expiry_date">
                    
                                    @error('expiry_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-md-2 col-form-label text-md-right">Status</label>
                                <div class="col-md-6">
                                    <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" autocomplete="status">
                                        <option value="1" @if($configurations->status == 1) selected @endif>On</option>
                                        <option value="0" @if($configurations->status == 0) selected @endif>Off</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="maintain_inventory" class="col-md-2 col-form-label text-md-right">Maintain Inventory</label>
                                <div class="col-md-6">
                                    <select id="maintain_inventory" class="form-control @error('maintain_inventory') is-invalid @enderror" name="maintain_inventory" autocomplete="maintain_inventory">
                                        <option value="1" @if($configurations->maintain_inventory == 1) selected @endif>Yes</option>
                                        <option value="0" @if($configurations->maintain_inventory == 0) selected @endif>No</option>
                                    </select>
                                    @error('maintain_inventory')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="thank_note" class="col-md-2 col-form-label text-md-right">Thank Note</label>
                                <div class="col-md-6">
                                    <input id="thank_note" type="text" class="form-control @error('thank_note') is-invalid @enderror" name="thank_note" value="{{ old('thank_note') ?? $configurations->thank_note }}" autocomplete="thank_note">
                    
                                    @error('thank_note')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="terms_conditions" class="col-md-2 col-form-label text-md-right">Terms and Conditions</label>
                                <div class="col-md-6">
                                    <textarea id="terms_conditions" type="text" class="form-control @error('terms_conditions') is-invalid @enderror" name="terms_conditions" value="{{ old('terms_conditions') ?? $configurations->terms_conditions }}" autocomplete="terms_conditions">{{ $configurations->terms_conditions }}</textarea>
                    
                                    @error('terms_conditions')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="footer_text" class="col-md-2 col-form-label text-md-right">Footer Text</label>
                                <div class="col-md-6">
                                    <textarea id="footer_text" class="form-control @error('footer_text') is-invalid @enderror" name="footer_text" autocomplete="footer_text">{{ $configurations->footer_text }}</textarea>
                    
                                    @error('footer_text')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="footer_number" class="col-md-2 col-form-label text-md-right">Footer Number</label>
                                <div class="col-md-6">
                                    <input id="footer_number" type="text" class="form-control @error('footer_number') is-invalid @enderror" name="footer_number" value="{{ old('footer_number') ?? $configurations->footer_number }}" autocomplete="footer_number">
                    
                                    @error('footer_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary float-right" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        function onBackgroundImageChange(event)
        {
            document.getElementById("background-image").src = URL.createObjectURL(event.target.files[0]);
        }

        function onLogoChange(event)
        {
            document.getElementById("logo-image").src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
@endsection