<!-- add show class in ! class="modal fade show" !  like this  but css se display block kerwana smooth transition ke loiye...-->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Contact Owner</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <input type="text" id="contact-owner-customer-name" class="form-control" placeholder="Your Name">
                                <span class="ersrv-reservation-error contact-owner-customer-name"></span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <input type="email" id="contact-owner-customer-email" class="form-control" placeholder="Your Email">
                                <span class="ersrv-reservation-error contact-owner-customer-email"></span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <input type="text" id="contact-owner-customer-phone" class="form-control" placeholder="Your Phone Number">
                                <span class="ersrv-reservation-error contact-owner-customer-phone"></span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <input type="text" id="contact-owner-customer-query-subject" class="form-control" placeholder="Query Subject">
                                <span class="ersrv-reservation-error contact-owner-customer-query-subject"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <textarea id="contact-owner-customer-message" class="form-control" placeholder="Your Message" style="width: 100%; height: 100px;" spellcheck="false"></textarea>
                                <span class="ersrv-reservation-error contact-owner-customer-message"></span>
                            </div>
                            <div class="form-group text-right">
                                <button class="ersrv-submit-contact-owner-request btn btn-accent" type="button">Send Message</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> -->
        </div>
    </div>